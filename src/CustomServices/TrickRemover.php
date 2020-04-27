<?php

declare(strict_types=1);


namespace App\CustomServices;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Class TrickRemover
 * @package App\CustomServices
 */
class TrickRemover
{
    protected EntityManagerInterface $manager;

    /**
     * TrickRemover constructor.
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }


    /**
     * Removes a trick from database
     * @param $trick
     */
    public function removeTrick($trick)
    {
        //Removes trick
        $this->manager->remove($trick);
        $this->manager->flush();
    }
}
