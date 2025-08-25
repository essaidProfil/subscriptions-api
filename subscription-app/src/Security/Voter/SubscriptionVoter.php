<?php

namespace App\Security\Voter;

use App\Entity\Subscription;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SubscriptionVoter extends Voter
{
    public const VIEW = 'SUBSCRIPTION_VIEW';
    public const CANCEL = 'SUBSCRIPTION_CANCEL';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::CANCEL], true) && $subject instanceof Subscription;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!is_object($user)) return false;
        return $subject->getUser()->getId() === $user->getId();
    }
}
