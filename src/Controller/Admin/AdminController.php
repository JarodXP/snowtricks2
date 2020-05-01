<?php

declare(strict_types=1);


namespace App\Controller\Admin;

use App\CustomServices\TrickRemover;
use App\Entity\Trick;
use App\Entity\User;
use App\Form\PaginationFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminController
 * @package App\Controller\Admin
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/admin/tricks/{page}",name="admin-tricks")
     * @Route("/admin/{page}",name="admin")
     * @param int|null $page
     * @param Request $request
     * @return Response
     */
    public function displayTrickListAction(Request $request, int $page = null):Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', 'Access Denied!!');

        if (is_null($page)) {
            $page = 1;
        }

        //Default query parameters
        $queryParameters = [
            'offset' => 0,
            'order' => 'name',
            'direction' => 'DESC',
            'limit' => 5,
            'filter' => 'all'
            ];

        $paginationForm = $this->createForm(PaginationFormType::class, null, [
            'sortFieldList'=>['name','trickGroup', 'author', 'dateAdded', 'dateModified', 'status'],
            'filterFieldList' => ['all']
        ]);

        $paginationForm->handleRequest($request);

        if ($paginationForm->isSubmitted() && $paginationForm->isValid()) {

            //Sets the new parameters for the query
            $queryParameters['limit'] = $paginationForm->get('limit')->getData();
            $queryParameters['order'] = $paginationForm->get('order')->getData();
            $queryParameters['direction'] = $paginationForm->get('direction')->getData();

            //Sets the offset
            if (!is_null($page)) {
                $queryParameters['offset'] = ($page - 1)*$queryParameters['limit'];
            }
        }

        //Gets the trick list
        $tricks = $this->getDoctrine()
            ->getRepository(Trick::class)
            ->getAdminTrickList($queryParameters);

        //Gets the number of pages depending on the limit
        $pages = round(count($tricks) / (int) $queryParameters['limit']);

        return $this->render('admin\trick_list.html.twig', [
            'tricks' => $tricks,
            'paginationForm' => $paginationForm->createView(),
            'params' => $queryParameters,
            'route' => 'admin-tricks',
            'pages' => $pages,
            'currentPage' => $page
        ]);
    }

    /**
     * @Route("/admin/users",name="admin-users")
     * @return Response
     */
    public function displayUsersListAction(string $user = 'Wawa'):Response
    {
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['username' => $user]);

        return $this->render('admin\users_list.html.twig', [
            'users' => $users,
            'user' => $user
        ]);
    }

    /**
     * @Route("/admin/remove-trick/{trickSlug}",name="admin_remove_trick")
     * @ParamConverter("trick", options={"mapping": {"trickSlug": "slug"}})
     * @param Trick $trick
     * @param TrickRemover $remover
     * @return RedirectResponse
     */
    public function removeTrickAction(Trick $trick, TrickRemover $remover)
    {
        //Removes the trick
        $remover->removeTrick($trick);

        //Adds a flash message
        $this->addFlash('notice', 'The trick ' . $trick->getName() . ' has been removed.');

        return $this->redirectToRoute('admin-tricks');
    }
}
