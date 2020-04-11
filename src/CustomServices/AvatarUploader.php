<?php


namespace App\CustomServices;

use App\Entity\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AvatarUploader extends MediaUploader
{
    /**
     * Stores the uploaded avatar file and creates a Media Entity
     * @param UploadedFile $avatarFile
     * @return Media
     */
    public function createAvatarMedia(UploadedFile $avatarFile): Media
    {
        // Moves and renames the file to the directory where avatars are stored
        $newFilename = $this->storeUploadedFile($avatarFile, $this->mediaDir);

        //Creates the avatar media entity and sets its properties
        return $this->createMediaEntity($avatarFile, $newFilename, 'avatar');
    }
}
