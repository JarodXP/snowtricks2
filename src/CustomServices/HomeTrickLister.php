<?php

declare(strict_types=1);


namespace App\CustomServices;

use App\Entity\Trick;
use App\Form\HomeLimitFormType;
use App\Form\HomeFilterFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

/**
 * Class HomeTrickLister
 * @package App\CustomServices
 */
class HomeTrickLister extends AbstractLister
{
    public const FILTER_ID = 'filterId';
    public const TRICK_GROUP = 'trickGroup';
    public const TRICK_GROUP_ID = 'trickGroupId';


    private FormFactoryInterface $formFactory;
    private Security $security;

    /**
     * HomeTrickLister constructor.
     * @param EntityManagerInterface $manager
     * @param FormFactoryInterface $formFactory
     * @param Security $security
     */
    public function __construct(EntityManagerInterface $manager, FormFactoryInterface $formFactory, Security $security)
    {
        parent::__construct($manager);
        $this->formFactory = $formFactory;
        $this->security = $security;
    }

    /**
     * Gets the trick list and applies a filter to the list by owner or admin for the drafts tricks
     * @return array
     */
    public function getGrantedList():array
    {
        //Gets the list of tricks with the registered query parameters
        $list = $this->getList('getHomeTrickList');

        //Sets a new array to pick up only the granted tricks from the list
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
    protected function setQueryDefaultParameters(): void
    {
        //Sets the default value for trick list
        $this->queryParameters = [
            self::LIMIT_FIELD => 5,
            self::FILTER_ID => null
        ];
    }

    /**
     * @inheritDoc
     */
    protected function setQueryParametersFromForm(FormInterface $paginationForm, int $page = null, string $formName = null): void
    {
        $newParameters = [];

        //Sets the new parameters depending on the form type
        switch ($formName) {
            case 'home_filter_form':
                is_null($paginationForm->get(self::TRICK_GROUP)->getData())
                    ? $newParameters[self::FILTER_ID] = null
                    : $newParameters[self::FILTER_ID] = $paginationForm->get(self::TRICK_GROUP)->getData()->getId();
                $newParameters[self::LIMIT_FIELD] = (int) $paginationForm->get(self::LIMIT_FIELD)->getData();
            break;

            case 'home_limit_form':
                $newParameters[self::FILTER_ID] = $paginationForm->get(self::TRICK_GROUP_ID)->getData();
                $newParameters[self::LIMIT_FIELD] = (int) $paginationForm->get(self::LIMIT_FIELD)->getData() + 5;
            break;

            default: break;
        }

        //Merges the default parameters and the new parameters
        $this->queryParameters = array_merge($this->queryParameters, $newParameters);
    }

    /**
     * Returns the trick list and the parameters for the template (limit, filter)
     * @param Request $request
     * @return array
     */
    public function getTrickListAndParameters(Request $request)
    {
        //Creates the forms for limit and filter
        $forms = [
            'filterForm' => $this->formFactory->create(HomeFilterFormType::class),
            'limitForm' => $this->formFactory->create(HomeLimitFormType::class)
        ];

        //Sets the default parameters
        $this->className = Trick::class;
        $this->setQueryDefaultParameters();

        //Sets the query parameters depending on either FilterForm or LimitForm is submitted
        foreach ($forms as $form) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                //Sets the query parameters corresponding to the form
                $this->setQueryParametersFromForm($form, null, $form->getName());
            }
        }

        //Binds the query parameters to the responseVars array
        $responseVars = $this->getQueryParameters();

        //Get the tricks list
        $responseVars['tricks'] = $this->getGrantedList();

        //Adds the forms to response variables
        $responseVars['filterForm'] = $forms['filterForm']->createView();
        $responseVars['limitForm'] = $forms['limitForm']->createView();

        return $responseVars;
    }
}
