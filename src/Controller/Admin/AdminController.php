<?php

declare(strict_types=1);


namespace App\Controller\Admin;

use App\CustomServices\AdminLister;
use App\CustomServices\AbstractLister;
use App\CustomServices\EntityRemover;
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
     * @Route("/admin",name="admin")
     * @param int|null $page
     * @param Request $request
     * @return Response
     */
    public function displayTrickListAction(Request $request, AdminLister $lister, int $page = null):Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', 'Access Denied!!');

        if (is_null($page)) {
            $page = 1;
        }

        //Creates the form
        $paginationForm = $this->createForm(PaginationFormType::class, null, [
            'sortFieldList'=>['name','trickGroup', 'author', 'dateAdded', 'dateModified', 'status'],
            'filterFieldList' => ['all']
        ]);

        //Sets the parameters for the query
        $lister->setQueryParameters($request, Trick::class, $paginationForm, $page);

        //Gets the trick list
        $tricks = $lister->getList(AdminLister::ADMIN_TRICK_LIST);

        return $this->render('admin\trick_list.html.twig', [
            'tricks' => $tricks,
            'paginationForm' => $paginationForm->createView(),
            'params' => $lister->getQueryParameters(),
            'route' => 'admin-tricks',
            'pages' => $lister->getTotalPages($tricks),
            'currentPage' => $page
        ]);
    }

    /**
     * @Route("/admin/remove-trick/{trickSlug}",name="admin_remove_trick")
     * @ParamConverter("trick", options={"mapping": {"trickSlug": "slug"}})
     * @param Request $request
     * @param Trick $trick
     * @param EntityRemover $remover
     * @return RedirectResponse
     */
    public function removeTrickAction(Request $request, Trick $trick, EntityRemover $remover)
    {
        //Uses Security voter to grant access
        $this->denyAccessUnlessGranted('edit', $trick);

        //Removes the trick
        $removeResponse = $remover->removeEntity($request, $trick, 'delete-trick');

        //Adds a flash message
        $this->addFlash($removeResponse['flashType'], $removeResponse['message']);

        return $this->redirectToRoute('admin-tricks');
    }

    /**
     * @Route("/admin/users/{page}",name="admin-users")
     * @param Request $request
     * @param AdminLister $lister
     * @param int $page
     * @return Response
     */
    public function displayUsersListAction(Request $request, AdminLister $lister, int $page = null):Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', 'Access Denied!!');

        if (is_null($page)) {
            $page = 1;
        }

        $paginationForm = $this->createForm(PaginationFormType::class, null, [
            'sortFieldList'=>['username','firstName', 'lastName', 'dateAdded', 'email', 'roles'],
            'filterFieldList' => ['all']
        ]);

        $lister->setQueryParameters($request, User::class, $paginationForm, $page);

        $users = $lister->getList(AbstractLister::ADMIN_USER_LIST);

        return $this->render('admin\users_list.html.twig', [
            'users' => $users,
            'paginationForm' => $paginationForm->createView(),
            'params' => $lister->getQueryParameters(),
            'route' => 'admin-users',
            'pages' => $lister->getTotalPages($users),
            'currentPage' => $page
        ]);
    }

    /**
     * @Route("/admin/remove-user/{username}",name="admin_remove_user")
     * @ParamConverter("user", options={"mapping": {"username": "username"}})
     * @param Request $request
     * @param User $user
     * @param EntityRemover $remover
     * @return RedirectResponse
     */
    public function removeUserAction(Request $request, User $user, EntityRemover $remover)
    {
        //Uses Security voter to grant access
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        //Removes the trick
        $removeResponse = $remover->removeEntity($request, $user, 'delete-user');

        //Adds a flash message
        $this->addFlash($removeResponse['flashType'], $removeResponse['message']);

        return $this->redirectToRoute('admin-users');
    }
}
