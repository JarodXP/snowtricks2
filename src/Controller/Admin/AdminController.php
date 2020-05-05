<?php

declare(strict_types=1);


namespace App\Controller\Admin;

use App\CustomServices\AdminLister;
use App\CustomServices\AbstractLister;
use App\CustomServices\TrickRemover;
use App\Entity\Trick;
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
    public function displayTrickListAction(Request $request, int $page = null):Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', 'Access Denied!!');

        if (is_null($page)) {
            $page = 1;
        }

        //Default query parameters
        $queryParameters = [
            'offset' => 0,
            AbstractLister::ORDER_FIELD => 'name',
            AbstractLister::DIRECTION_FIELD => 'DESC',
            AbstractLister::LIMIT_FIELD => 5,
            'filter' => 'all'
            ];

        $paginationForm = $this->createForm(PaginationFormType::class, null, [
            'sortFieldList'=>['name','trickGroup', 'author', 'dateAdded', 'dateModified', 'status'],
            'filterFieldList' => ['all']
        ]);

        $paginationForm->handleRequest($request);

        if ($paginationForm->isSubmitted() && $paginationForm->isValid()) {

            //Sets the new parameters for the query
            $queryParameters[AbstractLister::LIMIT_FIELD] = $paginationForm->get(AbstractLister::LIMIT_FIELD)->getData();
            $queryParameters[AbstractLister::ORDER_FIELD] = $paginationForm->get(AbstractLister::ORDER_FIELD)->getData();
            $queryParameters[AbstractLister::DIRECTION_FIELD] = $paginationForm->get(AbstractLister::DIRECTION_FIELD)->getData();

            //Sets the offset
            if (!is_null($page)) {
                $queryParameters['offset'] = ($page - 1)*$queryParameters[AbstractLister::LIMIT_FIELD];
            }
        }

        //Gets the trick list
        $tricks = $this->getDoctrine()
            ->getRepository(Trick::class)
            ->getAdminTrickList($queryParameters);

        //Gets the number of pages depending on the limit
        $pages = round(count($tricks) / (int) $queryParameters[AbstractLister::LIMIT_FIELD]);

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
     * @Route("/admin/users/{page}",name="admin-users")
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

        $queryParameters = $lister->getQueryParameters($request, $paginationForm, $page);

        $tricks = $lister->getList(Trick::class, AbstractLister::ADMIN_TRICK_LIST);

        return $this->render('admin\trick_list.html.twig', [
            'tricks' => $tricks,
            'paginationForm' => $paginationForm->createView(),
            'params' => $queryParameters,
            'route' => 'admin-tricks',
            'pages' => $lister->getTotalPages($tricks),
            'currentPage' => $page
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
