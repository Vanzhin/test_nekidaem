<?php

namespace App\Service;

use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class Mailer
{

    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendDailyMail(User $user, PaginationInterface $pagination)
    {
        $email = (new TemplatedEmail())
            ->from(new Address('test@test.test', 'Test'))
            ->to(new Address($user->getEmail(), $user->getName()))
            ->subject("Hello")
            ->htmlTemplate('email/subscription.html.twig')
            ->context(
                [
                    'user' => $user,
                    'posts' => $pagination
                ]
            );
        $this->mailer->send($email);
    }
}