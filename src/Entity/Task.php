<?php

namespace App\Entity;

use App\Service\PathKeeper;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Task
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 */
class Task
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(
     *     type="integer"
     * )
     */
    private $id;

    /**
     * @ORM\Column(
     *     type="string",
     *     length=255
     * )
     */
    private $title;

    /**
     * @ORM\Column(
     *     type="datetime"
     * )
     */
    private $dateDeadline;

    /**
     * @ORM\Column(
     *     type="string",
     *     length=50
     * )
     */
    private $state;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\User",
     *     inversedBy="tasks"
     * )
     *
     * @ORM\JoinColumn(
     *     nullable=false
     * )
     */
    private $owner;

    /**
     * @ORM\Column(
     *     type="string",
     *     length=50
     * )
     */
    private $icon;


    /**
     * @return int|null
     */
    final public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * @return string|null
     */
    final public function getTitle(): ?string
    {
        return $this->title;
    }


    /**
     * @param string $title
     *
     * @return Task
     */
    final public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }


    /**
     * @return DateTimeInterface|null
     */
    final public function getDateDeadline(): ?DateTimeInterface
    {
        return $this->dateDeadline;
    }


    /**
     * @param DateTimeInterface $dateDeadline
     *
     * @return Task
     */
    final public function setDateDeadline(DateTimeInterface $dateDeadline): self
    {
        $this->dateDeadline = $dateDeadline;

        return $this;
    }


    /**
     * @return string|null
     */
    final public function getState(): ?string
    {
        return $this->state;
    }


    /**
     * @param string $state
     *
     * @return Task
     */
    final public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }


    /**
     * @return User|null
     */
    final public function getOwner(): ?User
    {
        return $this->owner;
    }


    /**
     * @param User|null $owner
     *
     * @return Task
     */
    final public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }


    /**
     * @return string|null
     */
    final public function getIcon(): ?string
    {
        return PathKeeper::UPLOADED_ICONS_DIR.'/'.$this->icon;
    }


    /**
     * @param string $icon
     *
     * @return Task
     */
    final public function setIcon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }
}
