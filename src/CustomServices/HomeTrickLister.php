<?php

declare(strict_types=1);


namespace App\CustomServices;

use App\Entity\Trick;
use App\Form\HomeLimitFormType;
use App\Form\HomeListFormType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class HomeTrickLister
 * @package App\CustomServices
 */
class HomeTrickLister
{
    public const HOME_FORM = 'homeForm';
    public const LIMIT_FORM = 'limitForm';
    public const FILTER_ID = 'filterId';
    public const TRICK_GROUP = 'trickGroup';

    private EntityManagerInterface $manager;
    private FormFactoryInterface $formFactory;

    /**
     * HomeTrickLister constructor.
     * @param EntityManagerInterface $manager
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(EntityManagerInterface $manager, FormFactoryInterface $formFactory)
    {
        $this->manager = $manager;
        $this->formFactory = $formFactory;
    }

    /**
     * Gets the trick list for the home page depending on the filter and limit
     * @param array $responseVars
     * @param Request $request
     * @return array
     */
    public function getTrickList(Request $request, array $responseVars = null):array
    {
        //Creates the form for group filter
        $responseVars[self::HOME_FORM] = $this->formFactory->create(HomeListFormType::class);
        $responseVars[self::HOME_FORM]->handleRequest($request);

        //Creates the form for more tricks
        $responseVars[self::LIMIT_FORM] = $this->formFactory->create(HomeLimitFormType::class);
        $responseVars[self::LIMIT_FORM]->handleRequest($request);

        if ($responseVars[self::HOME_FORM]->isSubmitted() && $responseVars[self::HOME_FORM]->isValid()) {

            //Sets the trick group filter
            if (!is_null($responseVars[self::HOME_FORM]->get(self::TRICK_GROUP)->getData())) {
                $responseVars[self::FILTER_ID] = $responseVars[self::HOME_FORM]->get(self::TRICK_GROUP)->getData()->getId();
            } else {
                $responseVars[self::FILTER_ID] = null;
            }

            //Sets the limit of displayed tricks
            if (!is_null($responseVars[self::HOME_FORM]->get(TrickRepository::LIMIT_FIELD)->getData())) {
                $responseVars[TrickRepository::LIMIT_FIELD] = (int) $responseVars[self::HOME_FORM]->get(TrickRepository::LIMIT_FIELD)->getData();
            } else {
                $responseVars[TrickRepository::LIMIT_FIELD] = 5;
            }
        } elseif ($responseVars[self::LIMIT_FORM]->isSubmitted() && $responseVars[self::LIMIT_FORM]->isValid()) {

            //Sets the trick group filter
            if (!is_null($responseVars[self::LIMIT_FORM]->get(self::TRICK_GROUP)->getData())) {
                $responseVars[self::FILTER_ID] = (int) $responseVars[self::LIMIT_FORM]->get(self::TRICK_GROUP)->getData();
            } else {
                $responseVars[self::FILTER_ID] = null;
            }

            //Sets the limit of displayed tricks
            if (!is_null($responseVars[self::LIMIT_FORM]->get(TrickRepository::LIMIT_FIELD)->getData())) {
                $responseVars[TrickRepository::LIMIT_FIELD] = (int) $responseVars[self::LIMIT_FORM]->get(TrickRepository::LIMIT_FIELD)->getData() + 5;
            } else {
                $responseVars[TrickRepository::LIMIT_FIELD] = 5;
            }
        }

        //Gets the list of tricks
        $tricks = $this->manager
            ->getRepository(Trick::class)
            ->getHomeTrickList($responseVars[TrickRepository::LIMIT_FIELD], $responseVars[self::FILTER_ID]);

        $responseVars['tricks'] = $tricks;

        return $responseVars;
    }
}
