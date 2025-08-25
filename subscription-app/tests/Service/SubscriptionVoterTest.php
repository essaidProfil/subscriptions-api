<?php

declare(strict_types=1);

namespace App\Tests\Security\Voter;

use App\Entity\Subscription;
use App\Entity\User;
use App\Security\Voter\SubscriptionVoter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Unit tests for SubscriptionVoter.
 *
 * @internal
 */
#[CoversClass(SubscriptionVoter::class)]
#[Group('unit')]
final class SubscriptionVoterTest extends TestCase
{
    /**
     * Voter allows actions for owner and denies others.
     *
     * @return void
     * @throws ReflectionException|Exception
     */
    #[Test]
    public function test_vote_grants_when_owner_and_denies_otherwise(): void
    {
        $ownerUser = $this->createMock(User::class);
        $ownerUser->method('getId')->willReturn(10);

        $otherUser = $this->createMock(User::class);
        $otherUser->method('getId')->willReturn(20);

        $ownedSubscription = new Subscription();

        // Inject "user" relation via reflection (no Doctrine in unit scope)
        $userProperty = new \ReflectionProperty(Subscription::class, 'user');
        $userProperty->setAccessible(true);
        $userProperty->setValue($ownedSubscription, $ownerUser);

        $tokenForOwner = $this->createMock(TokenInterface::class);
        $tokenForOwner->method('getUser')->willReturn($ownerUser);

        $tokenForOther = $this->createMock(TokenInterface::class);
        $tokenForOther->method('getUser')->willReturn($otherUser);

        $voter = new SubscriptionVoter();

        $this->assertTrue($this->invokeVote($voter, SubscriptionVoter::VIEW, $ownedSubscription, $tokenForOwner));
        $this->assertTrue($this->invokeVote($voter, SubscriptionVoter::CANCEL, $ownedSubscription, $tokenForOwner));
        $this->assertFalse($this->invokeVote($voter, SubscriptionVoter::VIEW, $ownedSubscription, $tokenForOther));
    }

    /**
     * Helper to call protected voteOnAttribute via reflection.
     *
     * @param SubscriptionVoter $voter
     * @param string $attribute
     * @param Subscription $subject
     * @param TokenInterface $token
     * @return bool
     * @throws ReflectionException
     */
    private function invokeVote(SubscriptionVoter $voter, string $attribute, Subscription $subject, TokenInterface $token): bool
    {
        $ref = new \ReflectionClass($voter);
        $method = $ref->getMethod('voteOnAttribute');
        $method->setAccessible(true);
        /** @var bool $result */
        $result = $method->invoke($voter, $attribute, $subject, $token);
        return $result;
    }
}
