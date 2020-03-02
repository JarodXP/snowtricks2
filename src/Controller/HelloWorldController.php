<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HelloWorldController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function helloWorldAction()
    {
        return $this->render('hello_world.html.twig');
    }
}