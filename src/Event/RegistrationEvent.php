<?php

namespace App\Event;

use App\Entity\User;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class RegistrationEvent
 * @package App\Event
 */
class RegistrationEvent extends Event
{
    /** @var string NAME */
    public const NAME = 'user.register';

    /** @var User */
    protected $user;

    /**
     * RegistrationEvent constructor
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }


    /**
     * @return User
     */
    final public function getUser(): User
    {
        return $this->user;
    }
}
