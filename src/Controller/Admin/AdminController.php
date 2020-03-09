<?php


namespace App\Controller\Admin;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/tricks",name="admin-tricks")
     * @return Response
     */
    public function displayTrickListAction():Response
    {
        return $this->render('admin\trick_list.html.twig');
    }

    /**
     * @Route("/admin/users",name="admin-users")
     * @return Response
     */
    public function displayUsersListAction():Response
    {
        return $this->render('admin\users_list.html.twig');
    }

}