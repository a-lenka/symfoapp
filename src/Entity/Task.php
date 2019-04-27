<?php

namespace App\Entity;

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
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;


    /**
     * @ORM\Column(type="datetime")
     */
    private $dateDeadline;


    /**
     * @ORM\Column(type="string", length=50)
     */
    private $state;


    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }


    /**
     * @param string $title
     *
     * @return Task
     */
    public function setTitle(string $title): self
    {
        $this->name = $title;

        return $this;
    }


    /**
     * @return \DateTimeInterface|null
     */
    public function getDateDeadline(): ?\DateTimeInterface
    {
        return $this->dateDeadline;
    }


    /**
     * @param \DateTimeInterface $dateDeadline
     *
     * @return Task
     */
    public function setDateDeadline(\DateTimeInterface $dateDeadline): self
    {
        $this->dateDeadline = $dateDeadline;

        return $this;
    }


    /**
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->state;
    }


    /**
     * @param string $state
     *
     * @return Task
     */
    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }
}
