<?php
namespace App\Security;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Entity\User;

/**
 * Add user roles into the JWT payload.
 */
final class JWTCreatedSubscriber implements EventSubscriberInterface
{
    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [ Events::JWT_CREATED => 'onJWTCreated' ];
    }

    /**
     * @param JWTCreatedEvent $event
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();
        if ($user instanceof User) {
            $payload = $event->getData();
            $payload['roles'] = $user->getRoles();
            $event->setData($payload);
        }
    }
}
