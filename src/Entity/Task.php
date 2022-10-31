<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\TaskRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

//API PLATFORM
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiFilter;

//WITH ATTRIBUTS
/*#[ApiResource(
    //security: 'is_granted("ROLE_VIEWER")',  ou global dans security.yaml
    normalizationContext: ['groups' => ['t_read:collection']],
    denormalizationContext:['groups' => ['t_patch:item']],
    collectionOperations: [
        'get'
    ],
    itemOperations: [
        'patch',
        'get' =>[
            'normalization_context' => ['groups' =>  ['t_read:collection', 't_read:item']]
        ]
    ]
)]
#[ApiFilter(
    SearchFilter::class, properties: ['name' => 'partial', 'category' => 'exact', 'status' => 'exact']
)]*/

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @ApiResource(
 *     normalizationContext = {"groups" = {"t_read:collection"} },
 *     denormalizationContext = {"groups" = {"t_patch:item"} },
 *     collectionOperations = {"get"},
 *     itemOperations =  {
 *        "get" = {
 *           "normalization_context" =  {"groups" =  {"t_read:collection", "t_read:item"} }
 *         },
 *       "Patch"
 *     }
 *)
 * @ApiFilter(SearchFilter::class, properties = {"name" = "partial", "category" = "exact", "status" = "exact"})
 */
class Task
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups ({"t_read:collection"})
     */
    //#[Groups('t_read:collection')]
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner le nom de la tâche")
     * @Assert\Length(min = 1, max = 255, minMessage="Vous devez utilisez {{ limit }} caractère minimun.", maxMessage="Ne pas dépasser {{ limit }} caractères.")
     * @Groups ({"t_read:collection','p_read:item','tc_read:item"})
     */
    //#[Groups(['t_read:collection','p_read:item','tc_read:item'])]
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(min = 1, minMessage="Vous devez utilisez {{ limit }} caractère minimun.")
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank(message="Veuillez renseigner la date de début")
     * @Groups ({"t_read:collection"})
     */
    //#[Groups('t_read:collection')]
    private $startDate;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank(message="Veuillez renseigner la date de fin")
     * @Groups ({"t_read:collection"})
     */
    //#[Groups('t_read:collection')]
    private $endDate;

    /**
     * @ORM\Column(type="time")
     * @Assert\NotBlank(message="Veuillez renseigner l'heure de début")
     * @Groups ({"t_read:collection"})
     */
    //#[Groups('t_read:collection')]
    private $startHour;

    /**
     * @ORM\Column(type="time")
     * @Assert\NotBlank(message="Veuillez renseigner l'heure de fin")
     * @Groups ({"t_read:collection"})
     */
    //#[Groups('t_read:collection')]
    private $endHour;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $attachment;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner l'état de la tâche")
     * @Assert\Length(min = 4, max = 255, minMessage="Vous devez utilisez {{limit}} caractère minimun.", maxMessage="Ne pas dépasser {{limit}} caractères.")
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity=TaskCategory::class, inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="Veuillez selectionner la catégorie de la tâche")
     * @Groups ({"t_read:item"})
     */
    //#[Groups(['t_read:item'])]
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity=Location::class, inversedBy="tasks")
     * @Assert\NotBlank(message="Veuillez selectionner un lieu")
     * @Groups ({"t_read:item"})
     */
    //#[Groups('t_read:item')]
    private $location;

    /**
     * @ORM\ManyToMany(targetEntity=Equipment::class, inversedBy="tasks")
     * @Groups ({"t_read:item","t_patch:item"})
     */
    //#[Groups(['t_read:item','t_patch:item'])]
    private $equipment;

    /**
     * @Assert\NotBlank(message="Veuillez liéer cette tâche à un projet")
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="tasks")
     * @Groups ({"t_read:item"})
     */
    //#[Groups('t_read:item')]
    private $project;

    /**
     * @ORM\Column(type="datetime")
     * @Groups ({"t_patch:item"})
     */
    //#[Groups(['t_patch:item'])]
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tasks")
     */
    private $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tasks")
     */
    private $updatedBy;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="subscribedTasks")
     */
    private $owners;

    /**
     * @ORM\OneToMany(targetEntity=Document::class, mappedBy="task")
     */
    private $documents;

    /**
     * @ORM\OneToMany(targetEntity=LogEvent::class, mappedBy="task")
     */
    private $logEvents;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $stream;

    public function __construct()
    {
        $this->equipment = new ArrayCollection();
        $this->owners = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->logEvents = new ArrayCollection();
    }

     /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        if (empty($this->getCreatedAt())) {
            $this->createdAt = new \DateTime();
        }
    }
    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getStartHour(): ?\DateTimeInterface
    {
        return $this->startHour;
    }

    public function setStartHour(?\DateTimeInterface $startHour): self
    {
        $this->startHour = $startHour;

        return $this;
    }

    public function getEndHour(): ?\DateTimeInterface
    {
        return $this->endHour;
    }

    public function setEndHour(?\DateTimeInterface $endHour): self
    {
        $this->endHour = $endHour;

        return $this;
    }

    public function getAttachment(): ?string
    {
        return $this->attachment;
    }

    public function setAttachment(?string $attachment): self
    {
        $this->attachment = $attachment;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCategory(): ?TaskCategory
    {
        return $this->category;
    }

    public function setCategory(?TaskCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return Collection|Equipment[]
     */
    public function getEquipment(): Collection
    {
        return $this->equipment;
    }

    public function addEquipment(Equipment $equipment): self
    {
        if (!$this->equipment->contains($equipment)) {
            $this->equipment[] = $equipment;
        }

        return $this;
    }

    public function removeEquipment(Equipment $equipment): self
    {
        $this->equipment->removeElement($equipment);

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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?User $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getOwners(): Collection
    {
        return $this->owners;
    }

    public function addOwner(User $owner): self
    {
        if (!$this->owners->contains($owner)) {
            $this->owners[] = $owner;
        }

        return $this;
    }

    public function removeOwner(User $owner): self
    {
        $this->owners->removeElement($owner);

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
            $document->setTask($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getTask() === $this) {
                $document->setTask(null);
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
            $logEvent->setTask($this);
        }

        return $this;
    }

    public function removeLogEvent(LogEvent $logEvent): self
    {
        if ($this->logEvents->removeElement($logEvent)) {
            // set the owning side to null (unless already changed)
            if ($logEvent->getTask() === $this) {
                $logEvent->setTask(null);
            }
        }

        return $this;
    }

    public function getStream(): ?bool
    {
        return $this->stream;
    }

    public function setStream(?bool $stream): self
    {
        $this->stream = $stream;

        return $this;
    }
}
