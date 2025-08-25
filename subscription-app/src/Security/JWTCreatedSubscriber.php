<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Entity\User;

final class JWTCreatedSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [Events::JWT_CREATED => 'onJWTCreated'];
    }

    public function onJWTCreated(JWTCreatedEvent $createdEvent): void
    {
        $user = $createdEvent->getUser();
        if ($user instanceof User) {
            $data = $createdEvent->getData();
            $data['roles'] = $user->getRoles();
            $createdEvent->setData($data);
        }
    }
}
