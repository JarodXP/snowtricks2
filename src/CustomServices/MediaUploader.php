<?php


namespace App\CustomServices;

use App\Entity\Media;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaUploader
{
    protected $mediaDir;

    public function __construct($mediaDir)
    {
        $this->mediaDir = $mediaDir;
    }

    /**
     * Stores the file into the requested directory and renames it with a random name
     * @param UploadedFile $uploadedFile
     * @param string $fileClass
     * @param string $storageDirectory
     * @return string
     * @throws FileException
     */
    public function storeUploadedFile(UploadedFile $uploadedFile, string $storageDirectory): string
    {
        //Sets the filename
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);

        // this is needed to safely include the file name as part of the URL
        $safeFilename = transliterator_transliterate(
            'Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
            $originalFilename
        );
        $newFilename = $safeFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();

        // Move the file to the directory where avatars are stored
        $uploadedFile->move(
            $storageDirectory,
            $newFilename
        );

        return $newFilename;
    }

    /**
     * Creates a Media Entity to register in database
     * @param UploadedFile $uploadedFile
     * @param string $fileName
     * @param string|null $alt
     * @return Media
     */
    public function createMediaEntity(UploadedFile $uploadedFile, string $fileName, string $alt = null):Media
    {
        $newMedia = new Media();
        $newMedia->setFileName($fileName)
            ->setAlt($alt)
            ->setMimeType($uploadedFile->getClientMimeType());

        return $newMedia;
    }
}
