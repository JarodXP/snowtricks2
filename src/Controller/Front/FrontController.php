<?php


namespace App\Controller\Front;

use App\Entity\Trick;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    /**
     * @Route("/",name="home")
     */
    public function displayFrontTrickListAction()
    {
        $tricks = $this->getDoctrine()
            ->getRepository(Trick::class)
            ->findAll();

        return $this->render('front/home.html.twig', ['tricks' => $tricks]);
    }

    /**
     * @Route("/tricks/{trickName}",name="trick")
     * @param string $trickName
     * @return Response
     */
    public function displayTrickAction(string $trickName)
    {
        $trick = $this->getDoctrine()
            ->getRepository(Trick::class)
            ->findOneBy(['name' => $trickName]);

        return $this->render('front\trick.html.twig', [
            'edit' => false,
            'trick' => $trick,
        ]);
    }

    /**
     * @Route("/tricks/edit-{trickName}",name="edit-trick")
     * @param string $trickName
     */
    public function editTrickAction(string $trickName)
    {
    }

    /**
     * @Route("/tricks/remove-{trickName}",name="remove-trick")
     * @param string $trickName
     * @return void
     */
    public function removeTrickAction(string $trickName)
    {
    }

    /**
     * @Route("/privacy",name="privacy")
     */
    public function privacyAction()
    {
        return $this->render('front\privacy.html.twig');
    }
}
