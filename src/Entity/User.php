<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="Veuillez renseigner votre mail")
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Veuillez renseigner un mot de passe")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner votre prénom")
     * @Assert\Length(min = 1, max = 255, minMessage="Vous devez utilisez {{ limit }} caractère minimun.", maxMessage="Ne pas dépasser {{ limit }} caractères.")
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner votre nom")
     * @Assert\Length(min = 1, max = 255, minMessage="Vous devez utilisez {{ limit }} caractère minimun.", maxMessage="Ne pas dépasser {{ limit }} caractères.")
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mobil;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $office;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner votre fonction")
     */
    private $job;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $department;

    /**
     * @ORM\OneToMany(targetEntity=Project::class, mappedBy="createdBy")
     */
    private $projects;

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="createdBy")
     */
    private $tasks;

    /**
     * @ORM\ManyToMany(targetEntity=Task::class, mappedBy="owners")
     */
    private $subscribedTasks;

    /**
     * @ORM\OneToMany(targetEntity=Log::class, mappedBy="user")
     */
    private $logs;

    /**
     * @ORM\OneToMany(targetEntity=Document::class, mappedBy="uploadedBy")
     */
    private $documents;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="createdBy", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=LogEvent::class, mappedBy="createdBy", orphanRemoval=true)
     */
    private $logEvents;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
        $this->tasks = new ArrayCollection();
        $this->subscribedTasks = new ArrayCollection();
        $this->logs = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->logEvents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getMobil(): ?string
    {
        return $this->mobil;
    }

    public function setMobil(?string $mobil): self
    {
        $this->mobil = $mobil;

        return $this;
    }

    public function getOffice(): ?string
    {
        return $this->office;
    }

    public function setOffice(?string $office): self
    {
        $this->office = $office;

        return $this;
    }

    public function getJob(): ?string
    {
        return $this->job;
    }

    public function setJob(string $job): self
    {
        $this->job = $job;

        return $this;
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function setDepartment(?string $department): self
    {
        $this->department = $department;

        return $this;
    }

    /**
     * @return Collection|Project[]
     */
    public function getUpdatedBy(): Collection
    {
        return $this->updatedBy;
    }

    public function addUpdatedBy(Project $updatedBy): self
    {
        if (!$this->updatedBy->contains($updatedBy)) {
            $this->updatedBy[] = $updatedBy;
            $updatedBy->setCreatedBy($this);
        }

        return $this;
    }

    public function removeUpdatedBy(Project $updatedBy): self
    {
        if ($this->updatedBy->removeElement($updatedBy)) {
            // set the owning side to null (unless already changed)
            if ($updatedBy->getCreatedBy() === $this) {
                $updatedBy->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Project[]
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->setUpdatedBy($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getUpdatedBy() === $this) {
                $project->setUpdatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setCreatedBy($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getCreatedBy() === $this) {
                $task->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Task[]
     */
    public function getSubscribedTasks(): Collection
    {
        return $this->subscribedTasks;
    }

    public function addSubscribedTask(Task $subscribedTask): self
    {
        if (!$this->subscribedTasks->contains($subscribedTask)) {
            $this->subscribedTasks[] = $subscribedTask;
            $subscribedTask->addOwner($this);
        }

        return $this;
    }

    public function removeSubscribedTask(Task $subscribedTask): self
    {
        if ($this->subscribedTasks->removeElement($subscribedTask)) {
            $subscribedTask->removeOwner($this);
        }

        return $this;
    }

    /**
     * @return Collection|Log[]
     */
    public function getLogs(): Collection
    {
        return $this->logs;
    }

    public function addLog(Log $log): self
    {
        if (!$this->logs->contains($log)) {
            $this->logs[] = $log;
            $log->setUser($this);
        }

        return $this;
    }

    public function removeLog(Log $log): self
    {
        if ($this->logs->removeElement($log)) {
            // set the owning side to null (unless already changed)
            if ($log->getUser() === $this) {
                $log->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Document[]
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setUploadedBy($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getUploadedBy() === $this) {
                $document->setUploadedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setCreatedBy($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getCreatedBy() === $this) {
                $comment->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|LogEvent[]
     */
    public function getLogEvents(): Collection
    {
        return $this->logEvents;
    }

    public function addLogEvent(LogEvent $logEvent): self
    {
        if (!$this->logEvents->contains($logEvent)) {
            $this->logEvents[] = $logEvent;
            $logEvent->setCreatedBy($this);
        }

        return $this;
    }

    public function removeLogEvent(LogEvent $logEvent): self
    {
        if ($this->logEvents->removeElement($logEvent)) {
            // set the owning side to null (unless already changed)
            if ($logEvent->getCreatedBy() === $this) {
                $logEvent->setCreatedBy(null);
            }
        }

        return $this;
    }
}
