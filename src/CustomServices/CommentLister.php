<?php

declare(strict_types=1);


namespace App\CustomServices;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\PaginationFormType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CommentLister
 * @package App\CustomServices
 */
class CommentLister
{
    private EntityManagerInterface $manager;
    private FormFactoryInterface $formFactory;

    /**
     * CommentLister constructor.
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager, FormFactoryInterface $formFactory)
    {
        $this->manager = $manager;
        $this->formFactory = $formFactory;
    }

    /**
     * Gets the paginated comment list depending on the trick and the page
     * @param Trick $trick
     * @param Request $request
     * @param int $page
     * @return Paginator
     */
    public function getCommentList(Request $request, Trick $trick, int $page = null):Paginator
    {
        $paginationForm = $this->formFactory->create(PaginationFormType::class);
        $paginationForm->handleRequest($request);

        //Sets the query parameters
        $queryParameters = [
            'trickId' => $trick->getId(),
            'limit' => CommentRepository::LIMIT_DISPLAY,
            'offset' => ($page - 1)*CommentRepository::LIMIT_DISPLAY
        ];

        return $this->manager->getRepository(Comment::class)->getPaginatedList($queryParameters);
    }
}
