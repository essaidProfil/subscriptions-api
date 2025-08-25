<?php

declare(strict_types=1);

namespace App\Tests\Security;

use App\Entity\User;
use App\Security\JWTCreatedSubscriber;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for JWTCreatedSubscriber.
 *
 * @internal
 */
#[CoversClass(JWTCreatedSubscriber::class)]
#[Group('unit')]
final class JWTCreatedSubscriberTest extends TestCase
{
    /**
     * Ensure roles are injected into JWT payload.
     *
     * @return void
     * @throws Exception
     */
    #[Test]
    public function test_on_jwt_created_injects_roles(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getRoles')->willReturn(['ROLE_USER', 'ROLE_ADMIN']);

        $payload = ['sub' => '123'];
        $header  = [];

        $event = new JWTCreatedEvent($payload, $header, $user);
        $subscriber = new JWTCreatedSubscriber();
        $subscriber->onJWTCreated($event);

        $data = $event->getData();
        $this->assertArrayHasKey('roles', $data);
        $this->assertSame(['ROLE_USER', 'ROLE_ADMIN'], $data['roles']);
    }
}
