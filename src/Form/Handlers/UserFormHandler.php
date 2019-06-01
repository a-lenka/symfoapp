<?php

namespace App\Form\Handlers;

use App\Entity\User;
use App\Service\FileUploader;
use App\Service\PathKeeper;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Manage Form for Task Entity
 *
 * Class UserFormHandler
 * @package App\Form\Handlers
 */
class UserFormHandler extends FormHandler
{
    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /**
     * UserFormHandler constructor
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param FileUploader                 $fileUploader
     */
    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        FileUploader                 $fileUploader
    ) {
        parent::__construct($fileUploader);

        $this->passwordEncoder = $passwordEncoder;
    }


    /**
     * @param User   $user
     * @param string $password
     *
     * @return User
     */
    final public function setUserPassword(User $user, string $password): User
    {
        $encoded = $this->passwordEncoder->encodePassword(
            $user, $password
        );

        return $user->setPassword($encoded);
    }


    /**
     * @param User         $user
     * @param UploadedFile $uploadedAvatar
     *
     * @return User
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    final public function setUserAvatar(User $user, UploadedFile $uploadedAvatar): User
    {
        $newAvatarName = $this->fileUploader->uploadEntityIcon(
            PathKeeper::UPLOADED_AVATARS_DIR,
            $uploadedAvatar,
            $user->getAvatar()
        );

        return $user->setAvatar($newAvatarName);
    }


    /**
     * Set encoded password and upload avatar
     * for the given User Entity
     *
     * @param FormInterface $form
     * @param User          $user
     *
     * @return User
     * @throws FileExistsException
     * @throws FileNotFoundException
     *
     */
    private function setDefaults(
        FormInterface $form,
        User          $user
    ): User {
        $user = $this->setUserPassword($user, $form['password']->getData());

        $uploadedAvatar = $form['avatar']->getData();

        if ($uploadedAvatar) {
            $user = $this->setUserAvatar($user, $uploadedAvatar);
        }

        return $user;
    }


    /**
     * Set needed params for flushing User Entity after success registration
     *
     * @param FormInterface $form
     * @param User          $user
     *
     * @return User
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    final public function setRegisterFormData(FormInterface $form, User $user): User
    {
        $user = $this->setDefaults($form, $user);

        $user->setEmail($form['email']->getData());
        $user->setTheme($user::DEFAULT_THEME);

        return $user;
    }


    /**
     * Set needed params for flushing User Entity after changing params in Account page
     *
     * @param FormInterface $form
     * @param User          $user
     *
     * @return User
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    final public function setAccountFormData(FormInterface $form, User $user): User
    {
        $user = $this->setDefaults($form, $user);

        $user->setTheme($form['theme']->getData());

        return $user;
    }


    /**
     * Set needed params for flushing User Entity after success creating
     *
     * @param FormInterface $form
     * @param User          $user
     *
     * @return User
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    final public function setCreateFormData(FormInterface $form, User $user): User
    {
        $user = $this->setDefaults($form, $user);

        $user->setEmail($form['email']->getData());
        $user->setTheme($user::DEFAULT_THEME);

        return $user;
    }


    /**
     * Set needed params for flushing User Entity after success updating
     *
     * @param FormInterface $form
     * @param User          $user
     *
     * @return User
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    final public function setUpdateFormData(FormInterface $form, User $user): User
    {
        $user = $this->setDefaults($form, $user);

        $user->setEmail($form['email']->getData());
        $user->setTheme($user::DEFAULT_THEME);

        return $user;
    }
}
