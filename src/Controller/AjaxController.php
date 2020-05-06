<?php

declare(strict_types=1);


namespace App\Controller;

use App\Controller\Front\FrontController;
use App\CustomServices\CommentLister;
use App\CustomServices\HomeTrickLister;
use App\CustomServices\EntityRemover;
use App\Entity\Trick;
use App\Entity\User;
use App\Form\SimplePaginationFormType;
use App\Repository\CommentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AjaxController
 * Controller for the AJAX requests
 * @package App\Controller
 */
class AjaxController extends AbstractController
{
    private CommentLister $lister;

    /**
     * AjaxController constructor.
     * @param CommentLister $lister
     */
    public function __construct(CommentLister $lister)
    {
        $this->lister = $lister;
    }

    /**
     * @Route("/ajax/remove-trick/{trickSlug}", name="ajax_remove_trick")
     * @ParamConverter("trick", options={"mapping": {"trickSlug": "slug"}})
     * @param Trick $trick
     * @param EntityRemover $remover
     * @param Request $request
     * @return Response
     */
    public function ajaxRemoveTrick(Trick $trick, EntityRemover $remover, Request $request):Response
    {
        //Uses Security voter to grant access
        $this->denyAccessUnlessGranted('edit', $trick);

        //Removes the trick and gets the http message and status code
        $removeResponse = $remover->removeEntity($request, $trick, 'delete-trick');

        return new Response($removeResponse['message'], $removeResponse['httpCode']);
    }

    /**
     * @Route("/ajax/remove-user/{username}", name="ajax_remove_user")
     * @ParamConverter("user", options={"mapping": {"username": "username"}})
     * @param User $user
     * @param EntityRemover $remover
     * @param Request $request
     * @return Response
     */
    public function ajaxRemoveUser(User $user, EntityRemover $remover, Request $request):Response
    {
        //Uses Security voter to grant access
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        //Removes the user and gets the http message and status code
        $removeResponse = $remover->removeEntity($request, $user, 'delete-user');

        return new Response($removeResponse['message'], $removeResponse['httpCode']);
    }

    /**
     * @Route("/ajax/home-tricks", name="ajax_home_tricks")
     * @param Request $request
     * @param HomeTrickLister $lister
     * @return Response
     */
    public function ajaxDisplayHomeList(Request $request, HomeTrickLister $lister)
    {
        $responseVars = $lister->getTrickList($request);

        return $this->render('front/_trick_list.html.twig', $responseVars);
    }

    /**
     * @Route("/ajax/trick-comments/{trickSlug}/{page}", name="ajax_trick_comments")
     * @ParamConverter("trick", options={"mapping": {"trickSlug": "slug"}})
     * @param Trick $trick
     * @param int $page
     * @param Request $request
     * @return Response
     */
    public function ajaxDisplayComments(Trick $trick, int $page, Request $request)
    {
        $comments = $this->lister->getCommentList($request, $trick, $page);

        //Creates pagination form
        $paginationForm = $this->createForm(SimplePaginationFormType::class);

        return $this->render('front\_comments.html.twig', [
            'comments' => $comments,
            'paginationForm' => $paginationForm->createView(),
            'currentPage' => $page,
            'pages' => round(count($comments))/CommentRepository::LIMIT_DISPLAY,
            'route' => 'ajax_trick_comments',
            FrontController::TRICK_VAR => $trick,
        ]);
    }

    /**
     * @Route("/ajax/trick-status/{trickSlug}", name="ajax_trick_status")
     * @ParamConverter("trick", options={"mapping": {"trickSlug": "slug"}})
     * @param Trick $trick
     * @param Request $request
     * @return Response
     */
    public function ajaxChangeStatus(Trick $trick, Request $request)
    {
        //Check token
        $submittedToken = $request->request->get('token');

        if ($this->isCsrfTokenValid('change-status', $submittedToken)) {

            //Toggles status
            $trick->setStatus(!$trick->getStatus());

            //Syncs with database
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($trick);
            $manager->flush();

            return $this->json(['status' => $trick->getStatus()]);
        }

        return new Response('Invalid token', 400);
    }
}
