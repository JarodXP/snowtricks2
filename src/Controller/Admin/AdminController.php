<?php


namespace App\Controller\Admin;

use App\Entity\Trick;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminController
 * @package App\Controller\Admin
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/admin/tricks",name="admin-tricks")
     * @return Response
     */
    public function displayTrickListAction():Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN','Access Denied!!');
        $tricks = $this->getDoctrine()
            ->getRepository(Trick::class)
            ->findAll();

        return $this->render('admin\trick_list.html.twig', [
            'tricks' => $tricks
        ]);
    }

    /**
     * @Route("/admin/users",name="admin-users")
     * @return Response
     */
    public function displayUsersListAction(string $user = 'Wawa'):Response
    {
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['username' => $user]);

        return $this->render('admin\users_list.html.twig', [
            'users' => $users,
            'user' => $user
        ]);
    }
}
