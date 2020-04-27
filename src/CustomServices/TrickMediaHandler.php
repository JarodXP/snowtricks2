<?php

declare(strict_types=1);


namespace App\CustomServices;

use App\Entity\Media;
use App\Entity\Trick;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class TrickMediaHandler
 * Handles the filesystem operations for Tricks' Media entities
 * @package App\CustomServices
 */
class TrickMediaHandler extends MediaUploader
{
    /**
     * Stores the uploaded trick media file and creates a Media Entity
     * @param UploadedFile $mediaFile
     * @param string $alt
     * @return Media
     */
    public function createTrickMedia(UploadedFile $mediaFile, string $alt = null): Media
    {
        // Moves and renames the file to the directory where trick medias are stored
        $newFilename = $this->storeUploadedFile($mediaFile, $this->mediaDir);

        //Creates the media entity and sets its properties
        return $this->createMediaEntity($mediaFile, $newFilename, $alt);
    }

    /**
     * Replaces a trick media file in the Entity AND in the file system
     * @param UploadedFile $newMediaFile
     * @param Media $media
     */
    public function replaceTrickMediaFile(UploadedFile $newMediaFile, Media $media): void
    {
        // Moves and renames the file to the directory where trick medias are stored
        $newFilename = $this->storeUploadedFile($newMediaFile, $this->mediaDir);

        //Gets the former filename
        $formerFileName = $media->getFileName();

        //Replaces the filename in the Media Entity
        $media->setFileName($newFilename);

        //Removes the former file
        unlink($this->mediaDir.'/'.$formerFileName);
    }

    /**
     * Binds the media to the trick and possibly sets the main image
     * @param Trick $trick
     * @param Media $media
     * @param FormInterface $mediaForm
     */
    public function bindToTrick(Trick $trick, Media $media, FormInterface $mediaForm)
    {
        if (is_null($media->getId())) {
            //Binds the media to the trick media collection
            $trick->addMedia($media);
        }

        //Possibly binds the media as main image
        if ($mediaForm->get('mainImage')->getData()) {
            $trick->setMainImage($media);
        }
        //Removes the main image if the checkbox was unchecked
        elseif (!is_null($trick->getMainImage()) && $trick->getMainImage()->getId() == $media->getId()) {
            $trick->setMainImage(null);
        }
    }

    /**
     * Gets the media file depending on the media type
     * @param FormInterface $mediaForm
     * @param string $mediaType
     * @return UploadedFile|null
     */
    public function getMediaFile(FormInterface $mediaForm, string $mediaType):?UploadedFile
    {
        //Gets the file depending on the media type
        if ($mediaType == 'video') {
            $mediaFile = $mediaForm->get('video')->getData();
        } else {
            $mediaFile = $mediaForm->get('image')->getData();
        }

        return $mediaFile;
    }
}
