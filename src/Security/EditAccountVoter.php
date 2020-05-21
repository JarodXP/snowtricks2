<?php

declare(strict_types=1);


namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;

/**
 * Class EditAccountVoter
 * @package App\Security
 */
class EditAccountVoter extends Voter
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
        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        //Gets the user corresponding to the session
        $user = $token->getUser();

        //Checks if user is correct
        if (!$user instanceof User) {
            return false;
        }

        $account = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($account, $user);
            case self::MODERATE:
                return $this->canModerate($user);
            default:
                throw new InvalidArgumentException("This attribute doesn't exist");
        }
    }

    /**
     * Allows user and admin to edit the account
     * @param User $account
     * @param User $user
     * @return bool
     */
    private function canEdit(User $account, User $user)
    {
        if ($this->canModerate($user)) {
            return true;
        }

        return $user->getId() === $account->getId();
    }

    /**
     * Allow access admin to edit users account
     * @param User $user
     * @return bool
     */
    private function canModerate(User $user)
    {
        return in_array('ROLE_ADMIN', $user->getRoles());
    }
}
