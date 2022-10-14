<?php

namespace App\Entity;

use App\Repository\LogEventRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LogEventRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class LogEvent
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="logEvents")
     */
    private $project;

    /**
     * @ORM\ManyToOne(targetEntity=Task::class, inversedBy="logEvents")
     */
    private $task;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="logEvents")
     * @ORM\JoinColumn(nullable=false)
     */
    private $createdBy;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=Applicant::class, inversedBy="logEvents")
     */
    private $ratedBy;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): self
    {
        $this->task = $task;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }
	
	/**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
       
		$this->createdAt = new \DateTime();
        
    }
	
    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getRatedBy(): ?Applicant
    {
        return $this->ratedBy;
    }

    public function setRatedBy(?Applicant $ratedBy): self
    {
        $this->ratedBy = $ratedBy;

        return $this;
    }
}
