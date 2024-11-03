<?php

namespace App\Security;

use App\Entity\Organizer;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class OrganizerAccessVoter extends Voter
{
    public const VIEW = 'view';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute !== self::VIEW) {
            return false;
        }

        if (!$subject instanceof Organizer) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        assert($subject instanceof Organizer);

        return $user === $subject->getManager();
    }
}
