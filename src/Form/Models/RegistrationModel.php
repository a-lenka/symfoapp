<?php

namespace App\Form\Models;

use App\Entity\User;

/**
 * Class RegistrationModel
 * @package App\Form\Models
 */
class RegistrationModel implements FormDataModelInterface
{
    /** @var string */
    private $email;

    /** @var string */
    private $password;

    /** @var string */
    private $avatar;

    /** @var string */
    private $theme;

    /** @var bool */
    private $termsAccepted;

    /**
     * RegistrationModelModel constructor
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->email         = $user->getEmail();
        $this->password      = $user->getPassword();
        $this->theme         = $user->getTheme();
        $this->termsAccepted = false;
        $this->avatar        = $user->getAvatar();
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
     * @return bool
     */
    final public function isTermsAccepted(): bool
    {
        return $this->termsAccepted;
    }

    /**
     * @param bool $termsAccepted
     */
    final public function setTermsAccepted(bool $termsAccepted): void
    {
        $this->termsAccepted = $termsAccepted;
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
}
