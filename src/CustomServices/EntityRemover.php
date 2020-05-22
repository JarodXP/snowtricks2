<?php

declare(strict_types=1);


namespace App\CustomServices;

use App\Entity\LegalPage;
use App\Entity\Trick;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Class EntityRemover
 * @package App\CustomServices
 */
class EntityRemover
{
    protected EntityManagerInterface $manager;
    protected CsrfTokenManagerInterface $tokenManager;

    /**
     * EntityRemover constructor.
     * @param EntityManagerInterface $manager
     * @param Security $security
     */
    public function __construct(EntityManagerInterface $manager, CsrfTokenManagerInterface $tokenManager)
    {
        $this->manager = $manager;
        $this->tokenManager = $tokenManager;
    }


    /**
     * Removes an Entity from database
     * @param Request $request
     * @param Removable $entity
     * @param string $tokenId
     * @return array
     */
    public function removeEntity(Request $request, Removable $entity, string $tokenId):array
    {
        try {
            //Gets the token submitted
            $submittedToken = $request->request->get('remove_token');

            //Checks if token is valid
            if ($this->tokenManager->isTokenValid(new CsrfToken($tokenId, $submittedToken))) {

                //Removes entity
                //If User, calls a special function to handle bound tricks
                if (get_class($entity) == User::class) {
                    $this->manager->getRepository(User::class)->remove($entity);
                } else {
                    $this->manager->remove($entity);
                }

                $this->manager->flush();

                return [
                    'flashType' => 'notice',
                    'message' => $this->getEntityName($entity) . ' has been removed.',
                    'httpCode' => 200
                ];
            } else {

                //Throws exception if CSRF token is invalid
                throw new InvalidCsrfTokenException('You are not allowed to do this operation', 500);
            }
        } catch (AccessDeniedHttpException | InvalidCsrfTokenException | RuntimeException $e) {

            //Sets the message and code to return to the calling controller
            $message = $e->getMessage();
            $errorCode = $e->getCode();

            return [
                'flashType' => 'error',
                'message' => $message,
                'httpCode' => $errorCode
            ];
        }
    }

    /**
     * Gets the element name to be displayed on flash message
     * @param Removable $entity
     * @return string
     */
    private function getEntityName(Removable $entity):string
    {
        switch (get_class($entity)) {
            case LegalPage::class:

            case Trick::class: return $entity->getName();
            break;

            case User::class: return $entity->getUsername();
            break;

            default: throw new InvalidArgumentException();
            break;
        }
    }
}
