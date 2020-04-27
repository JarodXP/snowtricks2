<?php

declare(strict_types=1);


namespace App\Controller;

use App\CustomServices\TrickRemover;
use App\Entity\Trick;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AjaxController
 * Controller for the AJAX requests
 * @package App\Controller
 */
class AjaxController extends AbstractController
{
    /**
     * @Route("/ajax/remove-trick/{trickSlug}", name="ajax_remove_trick")
     * @ParamConverter("trick", options={"mapping": {"trickSlug": "slug"}})
     * @param Trick $trick
     * @param TrickRemover $remover
     * @param Request $request
     * @return Response
     */
    public function ajaxRemoveTrickAction(Trick $trick, TrickRemover $remover, Request $request):Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', 'Access Denied!!');

        $submittedToken = $request->request->get('remove_token');


        try {
            if ($this->isCsrfTokenValid('delete-trick', $submittedToken)) {
                //Removes the trick
                $remover->removeTrick($trick);
                return new Response('The trick '.$trick->getName().' has been removed.', 200);
            }
        } catch (Exception $e) {
            //In case trick could'nt be removed, sends an error response
            return new Response($e->getMessage(), 500);
        }

        return new Response('You are not allowed to do this operation', 500);
    }
}
