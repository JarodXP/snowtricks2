<?php

declare(strict_types=1);


namespace App\Controller\Admin;

use App\CustomServices\AdminLister;
use App\CustomServices\AbstractLister;
use App\CustomServices\EntityRemover;
use App\CustomServices\SlugMaker;
use App\Entity\LegalPage;
use App\Entity\Trick;
use App\Entity\User;
use App\Form\LegalPagesType;
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

    /**
     * @Route("/admin/legal-pages/{page}",name="admin_legal_list")
     * @param Request $request
     * @param AdminLister $lister
     * @param int $page
     * @return Response
     */
    public function displayLegalListAction(Request $request, AdminLister $lister, int $page = null):Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', 'Access Denied!!');

        if (is_null($page)) {
            $page = 1;
        }

        $paginationForm = $this->createForm(PaginationFormType::class, null, [
            'sortFieldList'=>['name'],
            'filterFieldList' => ['all']
        ]);

        $lister->setQueryParameters($request, LegalPage::class, $paginationForm, $page);

        $legalPages = $lister->getList(AbstractLister::ADMIN_LEGAL_LIST);

        return $this->render('admin\legal_list.html.twig', [
            'legalPages' => $legalPages,
            'paginationForm' => $paginationForm->createView(),
            'params' => $lister->getQueryParameters(),
            'route' => 'admin_legal_list',
            'pages' => $lister->getTotalPages($legalPages),
            'currentPage' => $page
        ]);
    }

    /**
     * @Route("/admin/legal/edit/{slug}",name="admin_legal_page")
     * @ParamConverter("page", options={"mapping": {"slug": "slug"}})
     * @param LegalPage $page
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editLegalPageAction(Request $request, SlugMaker $slugMaker, LegalPage $page = null)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (is_null($page)) {
            $page = new LegalPage();
        }

        //Sets the slug maker to allow LegalPage Entity to transform name into slug (autowiring doesn't work on entities)
        $page->setSlugMaker($slugMaker);

        $pageForm = $this->createForm(LegalPagesType::class, $page);

        $pageForm->handleRequest($request);

        if ($pageForm->isSubmitted() && $pageForm->isValid()) {
            $manager = $this->getDoctrine()->getManager();

            $manager->persist($page);
            $manager->flush();

            $this->addFlash('notice', 'The page has been saved');

            return $this->redirectToRoute('admin_legal_page', [
                'slug' => $page->getSlug()
            ]);
        }

        return $this->render(
            'admin/legal_page.html.twig',
            [
                'page' => $page,
                'legalPageForm' => $pageForm->createView()
            ]
        );
    }

    /**
     * @Route("/admin/legal/remove/{slug}", name="remove_legal_page")
     * @ParamConverter("page", options={"mapping": {"slug": "slug"}})
     * @param Request $request
     * @param EntityRemover $remover
     * @param LegalPage $page
     * @return RedirectResponse
     */
    public function removeLegalPageAction(Request $request, EntityRemover $remover, LegalPage $page)
    {
        //Uses Security voter to grant access
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        //Removes the page
        $removeResponse = $remover->removeEntity($request, $page, 'delete-legal-page');

        //Adds a flash message
        $this->addFlash($removeResponse['flashType'], $removeResponse['message']);

        return $this->redirectToRoute('admin_legal_list');
    }
}
