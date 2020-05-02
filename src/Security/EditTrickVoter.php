<?php

declare(strict_types=1);


namespace App\Security;

use App\Entity\Trick;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;

/**
 * Class EditTrickVoter
 * @package App\Security
 */
class EditTrickVoter extends Voter
{
    public const EDIT = 'edit';
    public const MODERATE = 'moderate';

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject)
    {
        //Checks if attribute is supported
        if (!in_array($attribute, [self::EDIT,self::MODERATE])) {
            return false;
        }

        //Checks if subject is supported
        if (!$subject instanceof Trick) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        $trick = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($trick, $user);
            case self::MODERATE:
                return $this->canModerate($user);
            default:
                throw new InvalidArgumentException("This attribute doesn't exisit");
        }
    }

    /**
     * Allow trick author and admin to edit trick
     * @param Trick $trick
     * @param User $user
     * @return bool
     */
    private function canEdit(Trick $trick, User $user)
    {
        if ($this->canModerate($user)) {
            return true;
        }

        return $user === $trick->getAuthor();
    }

    /**
     * Allow access admin user to change status
     * @param User $user
     * @return bool
     */
    private function canModerate(User $user)
    {
        return in_array('ROLE_ADMIN', $user->getRoles());
    }
}
