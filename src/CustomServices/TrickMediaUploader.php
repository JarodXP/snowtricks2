<?php


namespace App\CustomServices;

use App\Entity\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TrickMediaUploader extends MediaUploader
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
}
