<?php


namespace App\Controller\Home;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="front_home")
     */
    public function homeAction()
    {
        return $this->render('front/Home.html.twig');
    }
}