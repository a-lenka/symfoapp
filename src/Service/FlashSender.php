<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class FlashSender
 * @package App\Service
 */
class FlashSender
{
    /** @var SessionInterface */
    private $session;

    /** @var string NOTICE_TYPE */
    private const NOTICE_TYPE = 'notice';

    /**
     * FlashSender constructor
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }


    /**
     * @param $message
     */
    final public function sendNotice(string $message): void
    {
        $this->send(self::NOTICE_TYPE, $message);
    }


    /**
     * @param string $type
     * @param string $message
     */
    private function send(string $type, string $message): void
    {
        $this->session->getFlashBag()->add($type, $message);
    }
}
