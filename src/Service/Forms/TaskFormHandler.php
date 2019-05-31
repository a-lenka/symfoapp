<?php

namespace App\Service\Forms;

use App\DomainManager\TaskManager;
use App\Entity\Task;
use App\Entity\User;
use App\Service\FileUploader;
use App\Service\PathKeeper;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Manage Form for Task Entity
 *
 * Class TaskFormHandler
 * @package App\Service\Forms
 */
class TaskFormHandler
{
    /** @var TaskManager */
    private $taskManager;

    /** @var FileUploader */
    private $fileUploader;

    /**
     * TaskFormHandler constructor
     *
     * @param TaskManager  $taskManager
     * @param FileUploader $fileUploader
     */
    public function __construct(TaskManager $taskManager, FileUploader $fileUploader) {
        $this->taskManager  = $taskManager;
        $this->fileUploader = $fileUploader;
    }


    /**
     * Check form and flush the given Task Entity
     *
     * @param Request       $request
     * @param FormInterface $form
     * @param Task          $task
     * @param User          $owner
     *
     * @return bool
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    final public function handle(
        Request       $request,
        FormInterface $form,
        Task          $task,
        User          $owner
    ): bool {
        $form->handleRequest($request);

        if($request->isMethod('POST')
            && $form->isSubmitted()
            && $form->isValid()
        ) {
            $task->setOwner($owner);

            $uploadedIcon = $form['icon']->getData();

            if($uploadedIcon) {
                $newIconName = $this->fileUploader->uploadEntityIcon(
                    PathKeeper::UPLOADED_ICONS_DIR,
                    $uploadedIcon,
                    $task->getIcon()
                );

                $task->setIcon($newIconName);
            }

            $this->taskManager->flushTask($task);

            return true;
        }

        return false;
    }
}
