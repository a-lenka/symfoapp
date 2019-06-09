<?php

namespace App\DomainManager;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Service\FileUploader;
use Doctrine\Common\Persistence\ObjectManager;
use League\Flysystem\FileNotFoundException;

/**
 * Class TaskDomainManager
 * @package App\DomainManager
 */
class TaskDomainManager extends DomainManager
{
    /** @var TaskRepository */
    private $repository;

    /**
     * TaskDomainManager constructor
     *
     * @param ObjectManager  $entityManager
     * @param TaskRepository $repository
     * @param FileUploader   $fileUploader
     */
    public function __construct(
        ObjectManager  $entityManager,
        TaskRepository $repository,
        FileUploader   $fileUploader
    ) {
        parent::__construct($entityManager, $fileUploader);

        $this->repository = $repository;
    }


    /**
     * @param int $id
     *
     * @return Task
     */
    final public function findOneById(int $id): Task
    {
        return $this->repository->find($id);
    }


    /**
     * @param array $ids
     *
     * @return array
     */
    final public function findMultiplyById(array $ids): array
    {
        $tasks = [];

        foreach($ids as $id) {
            $idx     = is_int($id) ? $id : $id[0];
            $task    = $this->findOneById($idx);
            $tasks[] = $task;
        }

        return $tasks;
    }


    /**
     * @param User   $user
     * @param string $sortProperty
     * @param string $sortOrder
     *
     * @return array
     */
    final public function sort(
        User   $user,
        string $sortProperty,
        string $sortOrder
    ): array {
        return $this->repository->sortByProperty(
            $user->getId(), $sortProperty, $sortOrder
        );
    }


    /**
     * @param User   $owner
     * @param string $query
     *
     * @return array
     */
    final public function search(User $owner, string $query): array
    {
        return $this->repository->searchByQuery($owner->getId(), $query);
    }


    /**
     * Remove the given Task Entity with the icon,
     * and flush to the Database
     *
     * @param int $id
     *
     * @throws FileNotFoundException
     */
    final public function deleteOneById(int $id): void
    {
        $idx  = is_int($id) ? $id : $id[0];
        $task = $this->findOneById($idx);
        $this->fileUploader->deleteAvatar($task->getIcon());

        $this->appEntityManager->remove($task);
        $this->appEntityManager->flush();
    }


    /**
     * Remove the given Task Entity with the icon,
     * but not flush to the Database
     *
     * @param Task $task
     *
     * @throws FileNotFoundException
     */
    final public function removeTask(Task $task): void
    {
        $this->fileUploader->deleteAvatar($task->getIcon());
        $this->appEntityManager->remove($task);
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
            $task = $this->findOneById($idx);
            $this->removeTask($task);
        }

        $this->appEntityManager->flush();
    }


    /**
     * Flushes Task Entity
     *
     * @param Task $task
     */
    final public function flushTask(Task $task): void
    {
        $this->appEntityManager->persist($task);
        $this->appEntityManager->flush();
    }
}
