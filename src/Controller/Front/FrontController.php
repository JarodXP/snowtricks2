<?php

declare(strict_types=1);


namespace App\Controller\Front;

use App\CustomServices\CommentLister;
use App\CustomServices\HomeTrickLister;
use App\CustomServices\SlugMaker;
use App\CustomServices\TrickMediaHandler;
use App\CustomServices\EntityRemover;
use App\Entity\Comment;
use App\Entity\EmbedMedia;
use App\Entity\LegalPage;
use App\Entity\Media;
use App\Entity\Trick;
use App\Form\EmbedMediaFormType;
use App\Form\TrickForm\CommentFormType;
use App\Form\TrickForm\TrickFormType;
use App\Form\TrickForm\TrickMediaFormType;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FrontController
 * @package App\Controller\Front
 */
class FrontController extends AbstractController
{
    public const TRICK_VAR = 'trick';
    public const TRICK_SLUG_VAR = 'trickSlug';
    public const EDIT_TRICK_ROUTE = 'edit-trick';

    /**
     * @Route("/",name="home")
     * @param Request $request
     * @param HomeTrickLister $lister
     * @return Response
     */
    public function displayFrontTrickListAction(Request $request, HomeTrickLister $lister)
    {
        $responseVars = $lister->getTrickListAndParameters($request);

        return $this->render('front/home.html.twig', $responseVars);
    }

    /**
     * @Route("/tricks/{trickSlug}",name="trick")
     * @ParamConverter("trick", options={"mapping": {"trickSlug": "slug"}})
     * @Route("/tricks/{trickSlug}/Comments-{page}",name="trick_comments")
     * @param Trick $trick
     * @param Request $request
     * @param CommentLister $lister
     * @param int|null $page
     * @return Response
     */
    public function displayTrickAction(Trick $trick, Request $request, CommentLister $lister, int $page = null)
    {
        //Creates a Comment Entity to possibly get the form data
        $comment = new Comment();
        $commentForm = $this->createForm(CommentFormType::class, $comment);

        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {

            //Binds the trick and the user to the comment
            $user = $this->getUser();
            $comment->setUser($user);
            $comment->setTrick($trick);

            //Syncs with database
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($comment);
            $manager->flush();
        }

        //Sets the page number if null
        if (is_null($page)) {
            $page = 1;
        }

        //Creates a new empty form
        $commentForm = $this->createForm(CommentFormType::class);

        //Gets the comment list and other variables for the template
        $templateVars = $lister->getCommentListAndParameters($request, $trick, $page);

        //Adds the other variables for the template
        $templateVars['route'] = 'trick_comments';
        $templateVars['editMode'] = false;
        $templateVars['commentForm'] = $commentForm->createView();

        return $this->render('front\trick.html.twig', $templateVars);
    }

    /**
     * @Route("/tricks/edit/{trickSlug}", name="edit-trick")
     * @ParamConverter("trick", options={"mapping": {"trickSlug": "slug"}, "strip_null": true})
     * @param Trick $trick
     * @param Request $request
     * @param SlugMaker $slugMaker
     * @return Response
     * @throws Exception
     */
    public function editTrickAction(?Trick $trick, Request $request, SlugMaker $slugMaker)
    {
        $this->denyAccessUnlessGranted('ROLE_ACTIVATED_USER');

        if (is_null($trick)) {
            $trick = new Trick();
        } else {
            $this->denyAccessUnlessGranted('edit', $trick);
        }

        //Sets the slug maker to allow Trick Entity to transform name into slug (autowiring doesn't work on entities)
        $trick->setSlugMaker($slugMaker);

        //Sets the author
        $trick->setAuthor($this->getUser());

        //Creates form and applies updates to the entity
        $trickForm = $this->createForm(TrickFormType::class, $trick);

        $trickForm->handleRequest($request);

        if ($trickForm->isSubmitted() && $trickForm->isValid()) {

            //Updates the trick in database
            $trick->setMainImageIfNull();
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($trick);
            $manager->flush();

            return $this->redirectToRoute(self::EDIT_TRICK_ROUTE, [self::TRICK_SLUG_VAR => $trick->getSlug()]);
        }

        //Creates the comment form to be displayed
        $commentForm = $this->createForm(CommentFormType::class);

        return $this->render('front\trick.html.twig', [
            'editMode' => true,
            'trickForm' => $trickForm->createView(),
            'commentForm' => $commentForm->createView(),
            self::TRICK_VAR => $trick,
        ]);
    }

    /**
     * @Route("media/edit-trick-media/{mediaType}/{trickSlug}/{mediaId}", name="trick_media")
     * @ParamConverter("trick", options={"mapping": {"trickSlug": "slug"}})
     * @ParamConverter("media", options={"mapping": {"mediaId": "id"}})
     * @param Request $request
     * @param string $mediaType
     * @param Trick $trick
     * @param TrickMediaHandler $mediaHandler
     * @param Media|null $media
     * @return Response
     */
    public function trickMediaAction(
        Request $request,
        string $mediaType,
        Trick $trick,
        TrickMediaHandler $mediaHandler,
        Media $media = null
    ) {
        //Instantiates a new Media entity if no media was found
        if (is_null($media)) {
            $media = new Media();
        }

        //Gives the form data to the media Entity
        $mediaForm = $this->createForm(TrickMediaFormType::class, $media);

        $mediaForm->handleRequest($request);

        if ($mediaForm->isSubmitted() && $mediaForm->isValid()) {

            //Gets the media file
            $mediaFile = $mediaHandler->getMediaFile($mediaForm, $mediaType);

            //Handles the uploaded file
            if (!is_null($mediaFile)) {
                if (!is_null($media->getId())) {

                    //Replaces the media if already existing
                    $mediaHandler->replaceTrickMediaFile($mediaFile, $media);
                } else {

                    //Moves the file and gets the filename
                    $media = $mediaHandler->createTrickMedia($mediaFile);
                }

                //Sets the mime type
                $media->setMimeType($mediaFile->getClientMimeType());
            }

            //Sets the media attribute
            $media->setAlt($mediaForm->get('alt')->getData());

            //Binds the media to the trick
            $mediaHandler->bindToTrick($trick, $media, $mediaForm);

            //Registers in database
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($media);
            $manager->persist($trick);
            $manager->flush();

            //Redirects to corresponding trick page
            return $this->redirectToRoute(self::EDIT_TRICK_ROUTE, [self::TRICK_SLUG_VAR => $trick->getSlug()]);
        }

        return $this->render('front/media.html.twig', [
            'media' => $media,
            'mediaType' => $mediaType,
            self::TRICK_VAR => $trick,
            'trickMediaForm' => $mediaForm->createView()
        ]);
    }

    /**
     * @Route("media/edit-embed-media/{trickSlug}/{mediaId}", name="embed_media")
     * @ParamConverter("trick", options={"mapping": {"trickSlug": "slug"}})
     * @ParamConverter("embedMedia", options={"mapping": {"mediaId": "id"}})
     * @param Request $request
     * @param Trick $trick
     * @param EmbedMedia $embedMedia
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function embedMediaAction(Request $request, Trick $trick, EmbedMedia $embedMedia = null)
    {
        //Instantiates a new Media entity if no media was found
        if (is_null($embedMedia)) {
            $embedMedia = new EmbedMedia();
        }

        //Gives the form data to the media Entity
        $mediaForm = $this->createForm(EmbedMediaFormType::class, $embedMedia);

        $mediaForm->handleRequest($request);

        if ($mediaForm->isSubmitted() && $mediaForm->isValid()) {

                //Binds the media to the trick
            $embedMedia->setTrick($trick);

            //Registers in database
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($embedMedia);
            $manager->flush();

            //Redirects to corresponding trick page
            return $this->redirectToRoute(self::EDIT_TRICK_ROUTE, [self::TRICK_SLUG_VAR => $trick->getSlug()]);
        }

        return $this->render('front/media.html.twig', [
            'media' => $embedMedia,
            'mediaType' => 'embed',
            self::TRICK_VAR => $trick,
            'trickMediaForm' => $mediaForm->createView()
        ]);
    }

    /**
     * @Route("media/remove-trick-media/{trickSlug}/{mediaId}", name="remove_trick_media")
     * @ParamConverter("trick", options={"mapping": {"trickSlug": "slug"}})
     * @ParamConverter("media", options={"mapping": {"mediaId": "id"}})
     * @param Media $media
     * @param Trick $trick
     * @param TrickMediaHandler $mediaHandler
     * @return Response
     */
    public function removeTrickMediaAction(Media $media, Trick $trick, TrickMediaHandler $mediaHandler)
    {
        $this->denyAccessUnlessGranted('edit', $trick);

        //Gets the manager
        $manager = $this->getDoctrine()->getManager();

        if ($trick->getMainImage() === $media) {

            //Sets the tricks' mainImage to null
            $trick->setMainImage(null);
        }

        //Updates manager
        $manager->persist($trick);
        $mediaHandler->removeMedia($media);

        //Syncs with database
        $manager->flush();
        $this->addFlash('notice', 'Your media has been removed');

        return $this->redirectToRoute(self::EDIT_TRICK_ROUTE, [self::TRICK_SLUG_VAR=>$trick->getSlug()]);
    }

    /**
     * @Route("media/remove-embed-media/{trickSlug}/{mediaId}", name="remove_embed_media")
     * @ParamConverter("trick", options={"mapping": {"trickSlug": "slug"}})
     * @ParamConverter("embedMedia", options={"mapping": {"mediaId": "id"}})
     * @param EmbedMedia $embedMedia
     * @param Trick $trick
     */
    public function removeEmbedMediaAction(EmbedMedia $embedMedia, Trick $trick)
    {
        $this->denyAccessUnlessGranted('edit', $trick);

        $manager = $this->getDoctrine()->getManager();

        $manager->remove($embedMedia);

        $manager->flush();
    }

    /**
     * @Route("/tricks/remove/{trickSlug}",name="remove-trick")
     * @ParamConverter("trick", options={"mapping": {"trickSlug": "slug"}})
     * @param Trick $trick
     * @param EntityRemover $remover
     * @return RedirectResponse
     */
    public function removeTrickAction(Request $request, Trick $trick, EntityRemover $remover)
    {
        //Uses Security voter to grant access
        $this->denyAccessUnlessGranted('edit', $trick);

        //Removes the trick
        $removeResponse = $remover->removeEntity($request, $trick, 'delete-trick');

        //Adds a flash message
        $this->addFlash($removeResponse['flashType'], $removeResponse['message']);

        return $this->redirectToRoute('home', ['_fragment'=>'trick-list']);
    }

    /**
     * @Route("/legal/{slug}", name="legal_page")
     * @ParamConverter("page", options={"mapping": {"slug": "slug"}})
     * @param LegalPage $page
     * @return Response
     */
    public function displayLegalPageAction(LegalPage $page)
    {
        return $this->render(
            'front/legal_page.html.twig',
            [
                'page' => $page
            ]
        );
    }
}
