<?php

declare(strict_types=1);


namespace App\CustomServices;

use App\Entity\Media;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class MediaUploader
 * Handles filesystem operations for Media entities
 * @package App\CustomServices
 */
class MediaUploader
{
    protected string $mediaDir;

    protected EntityManagerInterface $manager;

    /**
     * MediaUploader constructor.
     * @param string $mediaDir
     * @param EntityManagerInterface $manager
     */
    public function __construct(string $mediaDir, EntityManagerInterface $manager)
    {
        $this->mediaDir = $mediaDir;
        $this->manager = $manager;
    }

    /**
     * Gets the Media Dir set in the service parameters
     * @return string
     */
    public function getMediaDir(): string
    {
        return $this->mediaDir;
    }

    /**
     * Stores the file into the requested directory and renames it with a random name
     * @param UploadedFile $uploadedFile
     * @param string $storageDirectory
     * @return string
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

    /**
     * Removes a media and the corresponding file
     * @param Media $media
     */
    public function removeMedia(Media $media)
    {
        $this->manager->remove($media);

        unlink($this->mediaDir.'/'.$media->getFileName());
    }
}
