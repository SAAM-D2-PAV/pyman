<?php

namespace App\Entity;

use App\Repository\DocumentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass=DocumentRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Document
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez charger un document")
     */
    private $uploadName;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="documents")
     */
    private $project;

    /**
     * @ORM\Column(type="datetime")
     */
    private $uploadedAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="documents")
     */
    private $uploadedBy;

    /**
     * @ORM\ManyToOne(targetEntity=Task::class, inversedBy="documents")
     */
    private $task;

    /**
     * @ORM\ManyToOne(targetEntity=Equipment::class, inversedBy="documents")
     */
    private $Equipment;



     /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        
        $this->uploadedAt = new \DateTime();
        
    }
  
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUploadName(): ?string
    {
        return $this->uploadName;
    }

    public function setUploadName(string $uploadName): self
    {
        $this->uploadName = $uploadName;

        return $this;
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

    public function getUploadedAt(): ?\DateTimeInterface
    {
        return $this->uploadedAt;
    }

    public function setUploadedAt(\DateTimeInterface $uploadedAt): self
    {
        $this->uploadedAt = $uploadedAt;

        return $this;
    }

    public function getUploadedBy(): ?User
    {
        return $this->uploadedBy;
    }
   
    public function setUploadedBy(?User $uploadedBy): self
    {
        $this->uploadedBy = $uploadedBy;

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

    public function getEquipment(): ?Equipment
    {
        return $this->Equipment;
    }

    public function setEquipment(?Equipment $Equipment): self
    {
        $this->Equipment = $Equipment;

        return $this;
    }
}
