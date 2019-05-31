<?php

namespace App\DomainManager;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use Doctrine\Common\Persistence\ObjectManager;
use League\Flysystem\FileNotFoundException;

/**
 * Class UserManager
 * @package App\DomainManager
 */
class UserManager
{
    /** @var ObjectManager */
    private $entityManager;

    /** @var UserRepository */
    private $repository;

    /** @var FileUploader */
    private $fileUploader;

    /** @var string DEFAULT_THEME */
    public const DEFAULT_THEME = 'red lighten-2';

    /**
     * UserManager constructor
     *
     * @param ObjectManager  $entityManager
     * @param UserRepository $repository
     * @param FileUploader   $fileUploader
     */
    public function __construct(
        ObjectManager  $entityManager,
        UserRepository $repository,
        FileUploader   $fileUploader
    ) {
        $this->entityManager = $entityManager;
        $this->repository    = $repository;
        $this->fileUploader  = $fileUploader;
    }


    /**
     * @param int $id
     *
     * @return User
     */
    final public function findOneById(int $id): User
    {
        return $this->repository->find($id);
    }


    /**
     * @return array
     */
    final public function findAll(): array
    {
        return $this->repository->findAll();
    }


    /**
     * @param array $ids
     *
     * @return array
     */
    final public function findMultiplyById(array $ids): array
    {
        $users = [];

        foreach($ids as $id) {
            $user    = $this->repository->findOneBy(['id' => $id]);
            $users[] = $user;
        }

        return $users;
    }


    /**
     * @param string $property
     * @param string $order
     *
     * @return array
     */
    final public function sortByProperty(string $property, string $order): array
    {
        return $this->repository->sortByProperty($property, $order);
    }


    /**
     * @param array $users
     *
     * @return array
     */
    final public function sortAscByNumberOfTasks(array $users): array
    {
        usort($users, function(User $a, User $b) {
            return count($a->getTasks()) > count($b->getTasks());
        });

        return $users;
    }


    /**
     * @param array $users
     *
     * @return array
     */
    final public function sortDescByNumberOfTasks(array $users): array
    {
        usort($users, function(User $a, User $b) {
            return count($a->getTasks()) < count($b->getTasks());
        });

        return $users;
    }


    /**
     * @param string $query
     *
     * @return array
     */
    final public function search(string $query): array
    {
       return $this->repository->searchByQuery($query);
    }


    /**
     * @param int $id
     *
     * @throws FileNotFoundException
     */
    final public function deleteOneById(int $id): void
    {
        $user = $this->findOneById($id);
        $this->fileUploader->deleteAvatar($user->getAvatar());

        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }


    /**
     * @param array $ids
     *
     * @throws FileNotFoundException
     */
    final public function deleteMultiplyById(array $ids): void
    {
        foreach($ids as $id) {
            $user = $this->repository->findOneBy(['id' => $id]);
            $this->fileUploader->deleteAvatar($user->getAvatar());
            $this->entityManager->remove($user);
        }

        $this->entityManager->flush();
    }
}
