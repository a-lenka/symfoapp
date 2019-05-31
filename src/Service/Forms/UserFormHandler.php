<?php

namespace App\Service\Forms;

use App\Entity\User;
use App\Service\FileUploader;
use App\Service\PathKeeper;
use Doctrine\Common\Persistence\ObjectManager;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Manage Form for Task Entity
 *
 * Class TaskFormHandler
 * @package App\Service\Forms
 */
class UserFormHandler
{
    /** @var ObjectManager */
    private $entityManager;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /** @var FileUploader */
    private $fileUploader;

    /**
     * UserFormHandler constructor
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param ObjectManager                $entityManager
     * @param FileUploader                 $fileUploader
     */
    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        ObjectManager                $entityManager,
        FileUploader                 $fileUploader
    ) {
        $this->entityManager   = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->fileUploader    = $fileUploader;
    }


    /**
     * Check form and flush the given Task Entity
     *
     * @param Request       $request
     * @param FormInterface $form
     * @param User          $user
     *
     * @return bool
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    final public function handle(
        Request       $request,
        FormInterface $form,
        User          $user
    ): bool {
        $form->handleRequest($request);

        if($request->isMethod('POST')
            && $form->isSubmitted()
            && $form->isValid()
        ) {
            $password = $this->passwordEncoder->encodePassword(
                $user, $user->getPassword()
            );

            $user->setPassword($password);

            $uploadedAvatar = $form['avatar']->getData();

            if($uploadedAvatar) {
                $newAvatarName = $this->fileUploader->uploadEntityIcon(
                    PathKeeper::UPLOADED_AVATARS_DIR,
                    $uploadedAvatar,
                    $user->getAvatar()
                );

                $user->setAvatar($newAvatarName);
            }

            $this->flushUser($user);

            return true;
        }

        return false;
    }


    /**
     * Flushes User Entity
     *
     * @param User $user
     */
    private function flushUser(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
