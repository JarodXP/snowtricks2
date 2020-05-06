<?php

declare(strict_types=1);


namespace App\CustomServices;

use App\Controller\Front\FrontController;
use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\SimplePaginationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CommentLister
 * @package App\CustomServices
 */
class CommentLister extends AbstractLister
{
    private FormFactoryInterface $formFactory;

    /**
     * CommentLister constructor.
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager, FormFactoryInterface $formFactory)
    {
        parent::__construct($manager);
        $this->formFactory = $formFactory;
    }

    /**
     * @inheritDoc
     */
    protected function setQueryDefaultParameters(): void
    {
        $this->queryParameters[self::LIMIT_FIELD] = 5;
        $this->queryParameters[self::OFFSET_FIELD] = 0;
    }


    /**
     * @inheritDoc
     */
    protected function setQueryParametersFromForm(FormInterface $paginationForm, int $page, string $formName = null): void
    {
        //Calculates the offset
        ($page > 0)
            ? $offset = ($page - 1) * $this->queryParameters[self::LIMIT_FIELD]
            : $offset = 0;

        //Sets the new parameters
        $newParameters = [self::OFFSET_FIELD => $offset];

        //Merges the default and the new parameters
        $this->queryParameters = array_merge($this->queryParameters, $newParameters);
    }

    /**
     * Returns the trick list and the parameters for the template (limit, filter)
     * @param Request $request
     * @param Trick $trick
     * @param int|null $page
     * @return array
     */
    public function getCommentListAndParameters(Request $request, Trick $trick, int $page = null)
    {
        //Sets the bound trick
        $this->queryParameters['trickId'] = $trick->getId();

        //Creates the pagination form
        $paginationForm = $this->formFactory->create(SimplePaginationFormType::class);

        //Sets the query parameters for filterForm
        $this->setQueryParameters($request, Comment::class, $paginationForm, $page);

        $responseVars = $this->getQueryParameters();

        //Get the Comment list
        $responseVars['comments'] = $this->getList('getPaginatedList');

        //Adds the form to response variables
        $responseVars['paginationForm'] = $paginationForm->createView();

        //Adds the other variables for the template
        $responseVars['currentPage'] = $page;
        $responseVars['pages'] = $this->getTotalPages($responseVars['comments']);
        $responseVars[FrontController::TRICK_VAR] = $trick;

        return $responseVars;
    }
}
