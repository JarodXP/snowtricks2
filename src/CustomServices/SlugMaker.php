<?php

declare(strict_types=1);


namespace App\CustomServices;

use App\Entity\Trick;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class SlugMaker
 * Builds slug from string
 * @package App\CustomServices
 */
class SlugMaker
{
    protected string $slug;
    protected EntityManagerInterface $manager;

    /**
     * SlugMaker constructor.
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Transforms name into slug
     * @param string $name
     * @return string
     */
    public function sluggify(string $name)
    {
        //Creates slug with format xxx-xxx-xxx
        $slug = strtolower(str_replace(" ", "-", $name));

        //Checks if slug is unique
        $existingSlug = $this->manager->getRepository(Trick::class)->findOneBy(['slug' => $slug]);

        //As long as slug is not unique,
        $i = 1;

        while ($existingSlug) {
            $newSlug = $slug."-".$i;

            $existingSlug = $this->manager->getRepository(Trick::class)->findOneBy(['slug' => $newSlug]);

            $i ++;
        }

        //Sets the new slugname
        if (isset($newSlug)) {
            $slug = $newSlug;
        }

        return $slug;
    }
}
