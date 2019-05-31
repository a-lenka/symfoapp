<?php

namespace App\DomainManager;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use Doctrine\Common\Persistence\ObjectManager;
use League\Flysystem\FileNotFoundException;

/**
 * Class UserDomainManager
 * @package App\DomainManager
 */
class UserDomainManager extends DomainManager
{
    /** @var UserRepository */
    private $repository;

    /** @var string DEFAULT_THEME */
    public const DEFAULT_THEME = 'red lighten-2';

    /**
     * UserDomainManager constructor
     *
     * @param ObjectManager  $objectManager
     * @param UserRepository $repository
     * @param FileUploader   $fileUploader
     */
    public function __construct(
        ObjectManager  $objectManager,
        UserRepository $repository,
        FileUploader   $fileUploader
    ) {
        parent::__construct($objectManager, $fileUploader);

        $this->repository = $repository;
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
            $idx     = is_int($id) ? $id : $id[0];
            $user    = $this->findOneById($idx);
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
        $idx  = is_int($id) ? $id : $id[0];
        $user = $this->findOneById($idx);
        $this->fileUploader->deleteAvatar($user->getAvatar());

        $this->appEntityManager->remove($user);
        $this->appEntityManager->flush();
    }


    /**
     * Remove the given User Entity with the icon,
     * but not flush to the Database
     *
     * @param User $user
     *
     * @throws FileNotFoundException
     */
    final public function removeUser(User $user): void
    {
        $this->fileUploader->deleteAvatar($user->getAvatar());
        $this->appEntityManager->remove($user);
    }


    /**
     * @param array $ids
     *
     * @throws FileNotFoundException
     */
    final public function deleteMultiplyById(array $ids): void
    {
        foreach($ids as $id) {
            $idx  = is_int($id) ? $id : $id[0];
            $user = $this->findOneById($idx);
            $this->removeUser($user);
        }

        $this->appEntityManager->flush();
    }
}
