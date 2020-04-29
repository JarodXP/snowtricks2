<?php

declare(strict_types=1);


namespace App\CustomServices;

use App\Entity\Trick;
use App\Form\HomeLimitFormType;
use App\Form\HomeListFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class HomeTrickLister
 * @package App\CustomServices
 */
class HomeTrickLister
{
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
        $responseVars['homeForm'] = $this->formFactory->create(HomeListFormType::class);
        $responseVars['homeForm']->handleRequest($request);

        //Creates the form for more tricks
        $responseVars['limitForm'] = $this->formFactory->create(HomeLimitFormType::class);
        $responseVars['limitForm']->handleRequest($request);

        if ($responseVars['homeForm']->isSubmitted() && $responseVars['homeForm']->isValid()) {

            //Sets the trick group filter
            if (!is_null($responseVars['homeForm']->get('trickGroup')->getData())) {
                $responseVars['filterId'] = $responseVars['homeForm']->get('trickGroup')->getData()->getId();
            } else {
                $responseVars['filterId'] = null;
            }

            //Sets the limit of displayed tricks
            if (!is_null($responseVars['homeForm']->get('limit')->getData())) {
                $responseVars['limit'] = (int) $responseVars['homeForm']->get('limit')->getData();
            } else {
                $responseVars['limit'] = 5;
            }
        } elseif ($responseVars['limitForm']->isSubmitted() && $responseVars['limitForm']->isValid()) {

            //Sets the trick group filter
            if (!is_null($responseVars['limitForm']->get('trickGroup')->getData())) {
                $responseVars['filterId'] = (int) $responseVars['limitForm']->get('trickGroup')->getData();
            } else {
                $responseVars['filterId'] = null;
            }

            //Sets the limit of displayed tricks
            if (!is_null($responseVars['limitForm']->get('limit')->getData())) {
                $responseVars['limit'] = (int) $responseVars['limitForm']->get('limit')->getData() + 5;
            } else {
                $responseVars['limit'] = 5;
            }
        }

        //Gets the list of tricks
        $tricks = $this->manager
            ->getRepository(Trick::class)
            ->getHomeTrickList($responseVars['limit'], $responseVars['filterId']);

        $responseVars['tricks'] = $tricks;

        return $responseVars;
    }
}
