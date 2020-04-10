<?php


namespace App\Controller\Front;

use App\Entity\Trick;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
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
     * @param string $user
     * @return \Symfony\Component\HttpFoundation\Response
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
     * @Route("/member/profile-{username}", name="user-profile")
     */
    public function displayProfileAction(string $username)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findBy(['username' => $username]);

        return $this->render('front/user_profile.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/edit-{trickName}", name="edit-trick")
     * @param string $trickName
     */
    public function editTrick(string $trickName)
    {
    }

    /**
     * @Route("/remove-{trickName}", name="remove-trick")
     * @param string $trickName
     */
    public function removeTrick(string $trickName)
    {
    }
}
