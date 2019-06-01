<?php

namespace App\Form\Models;

use App\Entity\Task;
use DateTime;

/**
 * Class TaskTypeModel
 * @package App\Form\Models
 */
class TaskTypeModel
{
    /** @var string */
    private $title;

    /** @var DateTime */
    private $dateDeadline;

    /** @var string */
    private $state;

    /** @var string */
    private $icon;

    /**
     * TaskModel constructor
     *
     * @param Task $task
     */
    public function __construct(Task $task)
    {
        $this->title        = $task->getTitle();
        $this->dateDeadline = $task->getDateDeadline();
        $this->state        = $task->getState();
        $this->icon         = $task->getIcon();
    }


    /**
     * @return string
     */
    final public function getTitle(): ?string
    {
        return $this->title;
    }


    /**
     * @param string $title
     */
    final public function setTitle(string $title): void
    {
        $this->title = $title;
    }


    /**
     * @return DateTime
     */
    final public function getDateDeadline(): ?DateTime
    {
        return $this->dateDeadline;
    }


    /**
     * @param DateTime $dateDeadline
     */
    final public function setDateDeadline(DateTime $dateDeadline): void
    {
        $this->dateDeadline = $dateDeadline;
    }


    /**
     * @return string
     */
    final public function getState(): ?string
    {
        return $this->state;
    }


    /**
     * @param string $state
     */
    final public function setState(string $state): void
    {
        $this->state = $state;
    }


    /**
     * @return string
     */
    final public function getIcon(): ?string
    {
        return $this->icon;
    }


    /**
     * @param string $icon
     */
    final public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }
}
