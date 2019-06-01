<?php

namespace App\Form\Handlers;

use App\Entity\Task;
use App\Entity\User;
use App\Service\PathKeeper;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Manage Form for Task Entity
 *
 * Class TaskFormHandler
 * @package App\Form\Handlers
 */
class TaskFormHandler extends FormHandler
{
    /**
     * @param Task $task
     * @param File $uploadedIcon
     *
     * @return Task
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    final public function setTaskIcon(Task $task, File $uploadedIcon): Task
    {
        $newIconName = $this->fileUploader->uploadEntityIcon(
            PathKeeper::UPLOADED_ICONS_DIR,
            $uploadedIcon,
            $task->getIcon()
        );

        return $task->setIcon($newIconName);
    }


    /**
     * Check form and flush the given Task Entity
     *
     * @param FormInterface $form
     * @param Task          $task
     * @param User          $owner
     *
     * @return Task
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    private function setDefaults(FormInterface $form, Task $task, User $owner): Task
    {
        $task->setOwner($owner);

        $uploadedIcon = $form['icon']->getData();

        if($uploadedIcon) {
            $task = $this->setTaskIcon($task, $uploadedIcon);
        }

        return $task;
    }


    /**
     * Sets defaults for the given Task Entity
     *
     * @param FormInterface $form
     * @param Task          $task
     * @param User          $owner
     *
     * @return Task
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    final public function setCreateFormData(FormInterface $form, Task $task, User $owner): Task
    {
        $task = $this->setDefaults($form, $task, $owner);

        $task->setTitle($form['title']->getData());
        $task->setState($form['state']->getData());
        $task->setDateDeadline($form['dateDeadline']->getData());

        return $task;
    }


    /**
     * Sets defaults for the given Task Entity
     *
     * @param FormInterface $form
     * @param Task          $task
     * @param User          $owner
     *
     * @return Task
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    final public function setUpdateFormData(FormInterface $form, Task $task, User $owner): Task
    {
        $task = $this->setDefaults($form, $task, $owner);

        $task->setTitle($form['title']->getData());
        $task->setState($form['state']->getData());
        $task->setDateDeadline($form['dateDeadline']->getData());

        return $task;
    }
}
