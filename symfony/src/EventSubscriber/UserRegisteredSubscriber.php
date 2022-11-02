<?php

namespace App\EventSubscriber;

use App\Entity\Blog;
use App\Event\UserRegisteredEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserRegisteredSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function onUserRegistered(UserRegisteredEvent $event)
    {
        $user = $event->getUser();
        $blog = new Blog();
        $blog->setTitle($user->getName() . '\'s blog');
        $user->setBlog($blog);
        $this->em->persist($user);
        $this->em->flush();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserRegisteredEvent::class => 'onUserRegistered'

        ];
    }
}