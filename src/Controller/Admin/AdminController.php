<?php


namespace App\Controller\Admin;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin",name="admin")
     * @return Response
     */
    public function displayTrickListAction():Response
    {
        return $this->render('admin\trick_list.html.twig');
    }
}