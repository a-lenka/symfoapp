<?php


namespace App\Form\Models;

use App\Entity\User;

/**
 * Class UserTypeModel
 * @package App\Form\Models
 */
class UserTypeModel
{
    /** @var string */
    private $email;

    /** @var array */
    private $roles;

    /** @var string */
    private $password;

    /** @var string */
    private $avatar;

    /**
     * UserTypeModel constructor
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->email    = $user->getEmail();
        $this->roles    = $user->getRoles();
        $this->password = $user->getPassword();
        $this->avatar   = $user->getAvatar();
    }


    /**
     * @return string
     */
    final public function getAvatar(): ?string
    {
        return $this->avatar;
    }


    /**
     * @param string $avatar
     */
    final public function setAvatar(string $avatar): void
    {
        $this->avatar = $avatar;
    }


    /**
     * @return string
     */
    final public function getPassword(): ?string
    {
        return $this->password;
    }


    /**
     * @param string $password
     */
    final public function setPassword(string $password): void
    {
        $this->password = $password;
    }


    /**
     * @return string
     */
    final public function getEmail(): ?string
    {
        return $this->email;
    }


    /**
     * @param string $email
     */
    final public function setEmail(string $email): void
    {
        $this->email = $email;
    }


    /**
     * @return array
     */
    final public function getRoles(): ?array
    {
        return $this->roles;
    }


    /**
     * @param array $roles
     */
    final public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }
}
