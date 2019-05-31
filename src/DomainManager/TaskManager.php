<?php

namespace App\DomainManager;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Service\FileUploader;
use Doctrine\Common\Persistence\ObjectManager;
use League\Flysystem\FileNotFoundException;

/**
 * Class TaskManager
 * @package App\DomainManager
 */
class TaskManager
{
    /** @var ObjectManager */
    private $entityManager;

    /** @var TaskRepository */
    private $repository;

    /** @var FileUploader */
    private $fileUploader;

    /**
     * TaskManager constructor
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
        $this->entityManager = $entityManager;
        $this->repository    = $repository;
        $this->fileUploader  = $fileUploader;
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
     * @return array
     */
    final public function findMultiplyById(array $ids): array
    {
        $tasks = [];

        foreach($ids as $id) {
            $task    = $this->repository->findOneBy(['id' => $id]);
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
     * @param int $id
     *
     * @throws FileNotFoundException
     */
    final public function deleteOneById(int $id): void
    {
        $task = $this->findOneById($id);
        $this->fileUploader->deleteAvatar($task->getIcon());

        $this->entityManager->remove($task);
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
            $task = $this->repository->findOneBy(['id' => $id]);
            $this->fileUploader->deleteAvatar($task->getIcon());
            $this->entityManager->remove($task);
        }

        $this->entityManager->flush();
    }


    /**
     * Flushes Task Entity
     *
     * @param Task $task
     */
    final public function flushTask(Task $task): void
    {
        $this->entityManager->persist($task);
        $this->entityManager->flush();
    }

}
