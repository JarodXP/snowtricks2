<?php

declare(strict_types=1);


namespace App\CustomServices;

use App\Entity\Trick;
use App\Form\HomeLimitFormType;
use App\Form\HomeListFormType;
use App\Security\EditTrickVoter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Class HomeTrickLister
 * @package App\CustomServices
 */
class HomeTrickLister extends AbstractLister
{
    public const HOME_FORM = 'homeForm';
    public const LIMIT_FORM = 'limitForm';
    public const FILTER_ID = 'filterId';
    public const TRICK_GROUP = 'trickGroup';

    private FormFactoryInterface $formFactory;
    private Security $security;

    /**
     * HomeTrickLister constructor.
     * @param EntityManagerInterface $manager
     * @param FormFactoryInterface $formFactory
     * @param EditTrickVoter $voter
     * @param TokenInterface $token
     */
    public function __construct(EntityManagerInterface $manager, FormFactoryInterface $formFactory, Security $security)
    {
        parent::__construct($manager);
        $this->formFactory = $formFactory;
        $this->security = $security;
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
            if (!is_null($responseVars[self::HOME_FORM]->get(self::LIMIT_FIELD)->getData())) {
                $responseVars[self::LIMIT_FIELD] = (int) $responseVars[self::HOME_FORM]->get(self::LIMIT_FIELD)->getData();
            } else {
                $responseVars[self::LIMIT_FIELD] = 5;
            }
        } elseif ($responseVars[self::LIMIT_FORM]->isSubmitted() && $responseVars[self::LIMIT_FORM]->isValid()) {

            //Sets the trick group filter
            if (!is_null($responseVars[self::LIMIT_FORM]->get(self::TRICK_GROUP)->getData())) {
                $responseVars[self::FILTER_ID] = (int) $responseVars[self::LIMIT_FORM]->get(self::TRICK_GROUP)->getData();
            } else {
                $responseVars[self::FILTER_ID] = null;
            }

            //Sets the limit of displayed tricks
            if (!is_null($responseVars[self::LIMIT_FORM]->get(self::LIMIT_FIELD)->getData())) {
                $responseVars[self::LIMIT_FIELD] = (int) $responseVars[self::LIMIT_FORM]->get(self::LIMIT_FIELD)->getData() + 5;
            } else {
                $responseVars[self::LIMIT_FIELD] = 5;
            }
        }

        //Gets the list of tricks
        $responseVars['tricks'] = $this->filterList($this->manager
            ->getRepository(Trick::class)
            ->getHomeTrickList($responseVars[self::LIMIT_FIELD], $responseVars[self::FILTER_ID]));


        return $responseVars;
    }

    /**
     * Applies a filter to the list by owner or admin for the drafts tricks
     * @param Paginator $list
     * @return array
     */
    private function filterList(Paginator $list):array
    {
        $tricksList = [];
        $i = 0;

        //Checks if the user is granted to see the drafts
        foreach ($list as $trick) {
            if ($trick->getStatus() == 1 || $this->security->isGranted('edit', $trick)) {
                $tricksList[$i] = $trick;
                $i++;
            }
        }
        return $tricksList;
    }

    /**
     * @inheritDoc
     */
    protected function queryDefaultParameters(): void
    {
        // TODO: Implement queryDefaultParameters() method.
    }


    /**
     * @inheritDoc
     */
    protected function getQueryParametersFromForm(FormInterface $paginationForm, int $page): void
    {
        // TODO: Implement getQueryParametersFromForm() method.
    }
}
