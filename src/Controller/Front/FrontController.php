<?php

declare(strict_types=1);


namespace App\Controller\Front;

use App\CustomServices\CommentLister;
use App\CustomServices\HomeTrickLister;
use App\CustomServices\SlugMaker;
use App\CustomServices\TrickMediaHandler;
use App\CustomServices\EntityRemover;
use App\Entity\Comment;
use App\Entity\Media;
use App\Entity\Trick;
use App\Form\SimplePaginationFormType;
use App\Form\TrickForm\CommentFormType;
use App\Form\TrickForm\TrickFormType;
use App\Form\TrickForm\TrickMediaFormType;
use App\Repository\CommentRepository;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
    public const EDIT_TRICK_VAR = 'edit-trick';

    /**
     * @Route("/",name="home")
     * @param Request $request
     * @param HomeTrickLister $lister
     * @return Response
     */
    public function displayFrontTrickListAction(Request $request, HomeTrickLister $lister)
    {
        //Sets the default value for trick list
        $responseVars['limit'] = 5;
        $responseVars['filterId'] = null;

        //Get the tricks list
        $responseVars = $lister->getTrickList($request, $responseVars);

        //Adds the forms to response variables
        $responseVars['homeForm'] = $responseVars['homeForm']->createView();
        $responseVars['limitForm'] = $responseVars['limitForm']->createView();

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

        //Gets comment list
        //Sets the page number if null
        if (is_null($page)) {
            $page = 1;
        }

        $comments = $lister->getCommentList($request, $trick, $page);

        //Creates pagination form
        $paginationForm = $this->createForm(SimplePaginationFormType::class);

        return $this->render('front\trick.html.twig', [
            'editMode' => false,
            'comments' => $comments,
            'commentForm' => $commentForm->createView(),
            'paginationForm' =>$paginationForm->createView(),
            'currentPage' => $page,
            'pages' => round(count($comments))/CommentRepository::LIMIT_DISPLAY,
            'route' => 'trick_comments',
            self::TRICK_VAR => $trick,
        ]);
    }

    /**
     * @Route("/tricks/edit/{trickSlug}", name="edit-trick")
     * @ParamConverter("trick", options={"mapping": {"trickSlug": "slug"}, "strip_null": true})
     * @IsGranted({"ROLE_USER"})
     * @param Trick $trick
     * @param Request $request
     * @param SlugMaker $slugMaker
     * @return Response
     * @throws Exception
     */
    public function editTrickAction(?Trick $trick, Request $request, SlugMaker $slugMaker)
    {
        if (is_null($trick)) {
            $trick = new Trick();
        }

        //Sets the slug maker to allow Trick Entity to transform name into slug
        $trick->setSlugMaker($slugMaker);

        //Sets the author
        $trick->setAuthor($this->getUser());

        //Creates form and applies updates to the entity
        $trickForm = $this->createForm(TrickFormType::class, $trick, [
            'attr'=> ['id'=>'trick_form']
        ]);

        $trickForm->handleRequest($request);

        if ($trickForm->isSubmitted() && $trickForm->isValid()) {

            //Updates the trick in database
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($trick);
            $manager->flush();

            return $this->redirectToRoute(self::EDIT_TRICK_VAR, [self::TRICK_SLUG_VAR => $trick->getSlug()]);
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

            return $this->redirectToRoute(self::EDIT_TRICK_VAR, [self::TRICK_SLUG_VAR => $trick->getSlug()]);
        }

        return $this->render('front/media.html.twig', [
            'media' => $media,
            'mediaType' => $mediaType,
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
        //Gets the manager
        $manager = $this->getDoctrine()->getManager();

        //Sets the tricks' mainImage to null
        $trick->setMainImage(null);

        //Updates manager
        $manager->persist($trick);
        $mediaHandler->removeMedia($media);

        //Syncs with database
        $manager->flush();
        $this->addFlash('notice', 'Your media has been removed');

        return $this->redirectToRoute(self::EDIT_TRICK_VAR, [self::TRICK_SLUG_VAR=>$trick->getSlug()]);
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
     * @Route("/privacy",name="privacy")
     */
    public function privacyAction()
    {
        return $this->render('front\privacy.html.twig');
    }
}
