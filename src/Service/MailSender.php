<?php

namespace App\Service;

use App\Entity\User;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class MailSender
 * @package App\Service
 */
class MailSender
{
    /** @var Environment */
    private $twig;

    /** @var Swift_Mailer */
    private $mailer;

    /** @var string SENDER_EMAIL */
    private const SENDER_EMAIL = 'symfoapp@gmail.com';

    /**
     * MailSender constructor
     *
     * @param Swift_Mailer $mailer
     * @param Environment  $twig
     */
    public function __construct(Swift_Mailer $mailer, Environment $twig)
    {
        $this->twig   = $twig;
        $this->mailer = $mailer;
    }


    /**
     * @param User $user
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function sendMailOnRegister(User $user): void
    {
        $message = (new Swift_Message('Welcome to Symfoapp'))
            ->setFrom(self::SENDER_EMAIL)
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render(
                    'emails/registration.html.twig'
                ),
                'text/html'
            );

        $this->mailer->send($message);
    }
}
