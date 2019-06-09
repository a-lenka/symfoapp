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

    /** @staticvar array $noTasksFoundMessages */
    private static $noTasksFoundMessages = [
        'Create a task to see your progress',
        'You have no any task yet',
        'Will we do something?',
        'It seems there are no tasks found',
        'It looks like you have no tasks yet',
    ];

    /** @var array $noUsersFoundMessages */
    private static $noUsersFoundMessages = [
        'It seems there are no users found',
        'Nobody\'s here',
    ];

    /**
     * FlashSender constructor
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }


    /**
     * @param string $type
     * @param string $message
     */
    private function send(string $type, string $message): void
    {
        $this->session->getFlashBag()->add($type, $message);
    }


    /**
     * @param $message
     */
    private function sendNotice(string $message): void
    {
        $this->send(self::NOTICE_TYPE, $message);
    }


    /**
     * Send random `No tasks found` message
     */
    final public function sendNoTasksFound(): void
    {
        $this->sendNotice(
            self::$noTasksFoundMessages[
                array_rand(self::$noTasksFoundMessages)
            ]
        );
    }


    /**
     * Send random `No users found` message
     */
    final public function sendNoUsersFound(): void
    {
        $this->sendNotice(
            self::$noUsersFoundMessages[
                array_rand(self::$noUsersFoundMessages)
            ]
        );
    }
}
