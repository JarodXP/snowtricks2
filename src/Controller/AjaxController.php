<?php

declare(strict_types=1);


namespace App\Controller;

use App\Controller\Front\FrontController;
use App\CustomServices\CommentLister;
use App\CustomServices\HomeTrickLister;
use App\CustomServices\TrickRemover;
use App\Entity\Trick;
use App\Form\SimplePaginationFormType;
use App\Repository\CommentRepository;
use Exception;
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
     * @param TrickRemover $remover
     * @param Request $request
     * @return Response
     */
    public function ajaxRemoveTrick(Trick $trick, TrickRemover $remover, Request $request):Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', 'Access Denied!!');

        $submittedToken = $request->request->get('remove_token');


        try {
            if ($this->isCsrfTokenValid('delete-trick', $submittedToken)) {
                //Removes the trick
                $remover->removeTrick($trick);
                return new Response('The trick '.$trick->getName().' has been removed.', 200);
            }
        } catch (Exception $e) {
            //In case trick could'nt be removed, sends an error response
            return new Response($e->getMessage(), 500);
        }

        return new Response('You are not allowed to do this operation', 500);
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
}
