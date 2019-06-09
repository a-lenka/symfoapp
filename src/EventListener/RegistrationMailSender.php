<?php

namespace App\EventListener;

use App\Event\RegistrationEvent;
use App\Service\MailSender;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class RegistrationMailSender
 * @package App\EventListener
 */
class RegistrationMailSender implements EventSubscriberInterface
{
    /** @var MailSender */
    private $mailer;

    /**
     * RegistrationMailSender constructor
     *
     * @param MailSender $mailSender
     */
    public function __construct(MailSender $mailSender)
    {
        $this->mailer = $mailSender;
    }


    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            RegistrationEvent::NAME => 'onUserRegister',
        ];
    }


    /**
     * @param RegistrationEvent $event
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function onUserRegister(RegistrationEvent $event): void
    {
        $this->mailer->sendMailOnRegister($event->getUser());
    }
}
