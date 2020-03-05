<?php


namespace App\Controller\Front;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    /**
     * @Route("/tricks/{trickName}",name="trick")
     */
    public function displayTrickAction(string $trickName)
    {
        return $this->render('front\trick.html.twig',[
            'edit' => true
        ]);
    }
}