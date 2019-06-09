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
        $this->send(
            $user,
            'Welcome to Symfoapp',
            'emails/registration.html.twig'
        );
    }


    /**
     * @param User   $user
     * @param string $subject
     * @param string $template
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function send(User $user, string $subject, string $template): void
    {
        $message = (new Swift_Message($subject))
            ->setFrom(self::SENDER_EMAIL)
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render($template),
                'text/html'
            )
        ;

        $this->mailer->send($message);
    }
}
