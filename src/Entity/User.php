<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class User
 * @package App\Entity
 *
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @var int|null $id - User unique ID in the Database
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(
     *     type="integer"
     * )
     */
    private $id;

    /**
     * @var string|null $email - User unique ID in the system
     *
     * @ORM\Column(
     *     type="string",
     *     length=180,
     *     unique=true
     * )
     */
    private $email;

    /**
     * @var array $roles - User roles
     *
     * @ORM\Column(
     *     type="json"
     * )
     */
    private $roles = [];

    /**
     * @var string|null $password - The hashed password
     *
     * @ORM\Column(
     *     type="string"
     * )
     */
    private $password;

    /**
     * @var string|null $avatar - User avatar
     *
     * @ORM\Column(
     *     type="string",
     *     length=255
     * )
     */
    private $avatar;

    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\Task",
     *     mappedBy="owner",
     *     orphanRemoval=true
     * )
     */
    private $tasks;


    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }


    /**
     * @return int|null - User unique ID in the Database
     */
    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * @return string|null - User unique ID in the system
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }


    /**
     * @param string $email - User email
     *
     * @return User
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }


    /**
     * @return string - A visual identifier that represents this user.
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }


    /**
     * @return array - User roles
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // Guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }


    /**
     * @param array $roles - User roles
     *
     * @return User
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }


    /**
     * @return string - Hashed password
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }


    /**
     * @param string $password - Hashed password
     *
     * @return User
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }


    /**
     * @see UserInterface
     */
    public function getSalt(): void
    {
        // Not needed when using the "bcrypt" algorithm in `security.yaml`
    }


    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user,
        // clear it here.
        // $this->plainPassword = null;
    }


    /**
     * @param string $avatar
     *
     * @return User
     */
    public function setAvatar(string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }


    /**
     * @return string|null
     */
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }


    /**
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }


    /**
     * @param Task $task
     *
     * @return User
     */
    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setOwner($this);
        }

        return $this;
    }


    /**
     * @param Task $task
     *
     * @return User
     */
    public function removeTask(Task $task): self
    {
        if ($this->tasks->contains($task)) {
            $this->tasks->removeElement($task);

            // Set the owning side to null (unless already changed)
            if ($task->getOwner() === $this) {
                $task->setOwner(null);
            }
        }

        return $this;
    }

}
