<?php

namespace App\Form\Models;

use App\Entity\User;

/**
 * Class AccountPropertiesModel
 * @package App\Form\Models
 */
class AccountPropertiesModel implements FormDataModelInterface
{
    /** @var string */
    private $avatar;

    /** @var string */
    private $nickname;

    /** @var string */
    private $theme;

    /** @var string */
    private $password;

    /**
     * AccountPropertiesModel constructor
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->nickname = $user->getNickname();
        $this->avatar   = $user->getAvatar();
        $this->theme    = $user->getTheme();
        $this->password = $user->getPassword();
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
    final public function getNickname(): ?string
    {
        return $this->nickname;
    }


    /**
     * @param string $nickname
     */
    final public function setNickname(string $nickname): void
    {
        $this->theme = $nickname;
    }


    /**
     * @return string
     */
    final public function getTheme(): ?string
    {
        return $this->theme;
    }


    /**
     * @param string $theme
     */
    final public function setTheme(string $theme): void
    {
        $this->theme = $theme;
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
}
