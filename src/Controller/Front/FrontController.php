<?php


namespace App\Controller\Front;

use App\CustomServices\TrickMediaUploader;
use App\Entity\Media;
use App\Entity\Trick;
use App\Form\TrickForm\CommentFormType;
use App\Form\TrickForm\TrickFormType;
use App\Form\TrickForm\TrickMediaFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/tricks/edit/{trickName}",name="edit-trick")
     * @IsGranted({"ROLE_USER"})
     * @param string $trickName
     * @param Request $request
     * @return Response
     */
    public function editTrickAction(string $trickName, Request $request)
    {
        //Gets the trick in database
        $trick = $this->getDoctrine()
            ->getRepository(Trick::class)
            ->findOneBy(['name' => $trickName]);

        //Creates form and applies updates to the entity
        $trickForm = $this->createForm(TrickFormType::class, $trick);

        $trickForm->handleRequest($request);

        if ($trickForm->isSubmitted() && $trickForm->isValid()) {

            //Updates the trick in database
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($trick);
            $manager->flush();

            return $this->redirectToRoute('edit-trick', ['trickName' => $trick->getName()]);
        }

        //Creates the comment form to be displayed
        $commentForm = $this->createForm(CommentFormType::class);

        return $this->render('front\trick.html.twig', [
            'editMode' => true,
            'trickForm' => $trickForm->createView(),
            'commentForm' => $commentForm->createView(),
            'trick' => $trick,
        ]);
    }

    /**
     * @Route("edit-{trickName}/media/{mediaType}/{mediaId}", name="add_trick_media")
     * @ParamConverter("trick", options={"mapping": {"trickName": "name"}})
     * @ParamConverter("media", options={"mapping": {"mediaId": "id"}})
     * @param Request $request
     * @param string $mediaType
     * @param Trick $trick
     * @param TrickMediaUploader $uploader
     * @param Media|null $media
     * @return Response
     * @throws \Exception
     */
    public function addTrickMediaAction(Request $request, string $mediaType, Trick $trick, TrickMediaUploader $uploader, Media $media = null)
    {
        //Instantiates a new Media entity if no media was found
        if (is_null($media)) {
            $media = new Media();
        }

        //Gives the form data to the media Entity
        $mediaForm = $this->createForm(TrickMediaFormType::class, $media);

        $mediaForm->handleRequest($request);

        if ($mediaForm->isSubmitted() && $mediaForm->isValid()) {

            //Gets the file depending on the media type
            if ($mediaType == 'video') {
                $mediaFile = $mediaForm->get('video')->getData();
            } else {
                $mediaFile = $mediaForm->get('image')->getData();
            }

            if (!is_null($mediaType) && !is_null($media->getId())) {

                //Replaces the media if already existing
                $uploader->replaceTrickMediaFile($mediaFile, $media);
            } elseif (!is_null($mediaType) && is_null($media->getId())) {

                //Moves the file and gets the filename
                $media->setFileName($uploader->storeUploadedFile($mediaFile, $uploader->getMediaDir()));
            }

            //Sets the media attribute
            $media->setAlt($mediaForm->get('alt')->getData());
            $media->setMimeType($mediaFile->getClientMimeType());

            //Binds the media
            $trick->addMedia($media);

            //Registers in database
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($media);
            $manager->persist($trick);
            $manager->flush();

            return $this->redirectToRoute('edit-trick', ['trickName' => $trick->getName()]);
        }

        return $this->render('front/media.html.twig', [
            'media' => $media,
            'mediaType' => $mediaType,
            'trick' => $trick,
            'trickMediaForm' => $mediaForm->createView()
        ]);
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
