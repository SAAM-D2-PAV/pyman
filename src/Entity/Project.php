<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

//API PLATFORM
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

//WITH ATTRIBUTS
/*#[ApiResource(
    normalizationContext: ['groups' => ['p_read:collection']],
    collectionOperations: ['get'],
    itemOperations: [
        'get' =>[
            'normalization_context' => ['groups' =>  ['p_read:collection', 'p_read:item']]
        ]
    ]
)]*/
//OR ANNOTATIONS
/**
 * @ORM\Entity(repositoryClass=ProjectRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @ApiResource(
 *     normalizationContext = {"groups" = {"p_read:collection"} },
 *     collectionOperations = {"get"},
 *     itemOperations =  {"get" = {"normalization_context" = {"groups" =  {"p_read:collection", "p_read:item"} } } }
 *)
 */
class Project
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner le nom du projet")
     * @Assert\Length(min = 1, max = 255, minMessage="Vous devez utilisez {{ limit }} caractère minimun.", maxMessage="Ne pas dépasser {{ limit }} caractères.")
     * @Groups ({"p_read:collection","t_read:item"})
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Veuillez renseigner ce champ")
     */
    private $information;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(message="Veuillez renseigner ce champ")
     */
    private $deliveryDate;

    /**
     * @ORM\ManyToOne(targetEntity=ProjectCategory::class, inversedBy="projects")
     * @Assert\NotBlank(message="Veuillez renseigner ce champ")
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="project")
     * @Groups ({"p_read:item"})
     */
    private $tasks;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="projects")
     */
    private $createdBy;

     /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="projects")
     */
    private $updatedBy;

    /**
     * @ORM\ManyToOne(targetEntity=Applicant::class, inversedBy="projects")
     * @Assert\NotBlank(message="Veuillez renseigner ce champ")
     */
    private $requestBy;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner l'état de la tâche")
     * @Assert\Length(min = 4, max = 255, minMessage="Vous devez utilisez {{ limit }} caractère minimun.", maxMessage="Ne pas dépasser {{ limit }} caractères.")
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity=Document::class, mappedBy="project")
     */
    private $documents;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="project", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\Column(type="string", length=2000, nullable=true)
     * @Assert\Url(
     *    message = "L'url '{{ value }}' n'st pas valide",
     * )
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rating;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $note;

    /**
     * @ORM\OneToMany(targetEntity=LogEvent::class, mappedBy="project")
     */
    private $logEvents;

    /**
     * @ORM\OneToOne(targetEntity=ProjectRateByApplicant::class, mappedBy="project", cascade={"persist", "remove"})
     */
    private $applicantRating;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pub_video;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $pub_presentation;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $pub_video_status;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->comments = new ArrayCollection();
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

     /**
     * @ORM\PrePersist
     */
    // public function setCreatedAt(\DateTime $createdAt): self
    // {
    //     $this->createdAt = $createdAt;

    //     return $this;
    // }

    public function getInformation(): ?string
    {
        return $this->information;
    }

    public function setInformation(string $information): self
    {
        $this->information = $information;

        return $this;
    }

    public function getDeliveryDate(): ?\DateTimeInterface
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(?\DateTimeInterface $deliveryDate): self
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }

    public function getCategory(): ?ProjectCategory
    {
        return $this->category;
    }

    public function setCategory(?ProjectCategory $category): self
    {
        $this->category = $category;

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
            $task->setProject($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getProject() === $this) {
                $task->setProject(null);
            }
        }

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     *  @ORM\PreUpdate
     */
    public function setUpdatedAt(): self
    {
        $this->updatedAt = new \DateTime();

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

    public function getRequestBy(): ?Applicant
    {
        return $this->requestBy;
    }

    public function setRequestBy(?Applicant $requestBy): self
    {
        $this->requestBy = $requestBy;

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
            $document->setProject($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getProject() === $this) {
                $document->setProject(null);
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
            $comment->setProject($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getProject() === $this) {
                $comment->setProject(null);
            }
        }

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getRating(): ?string
    {
        return $this->rating;
    }

    public function setRating(?string $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

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
            $logEvent->setProject($this);
        }

        return $this;
    }

    public function removeLogEvent(LogEvent $logEvent): self
    {
        if ($this->logEvents->removeElement($logEvent)) {
            // set the owning side to null (unless already changed)
            if ($logEvent->getProject() === $this) {
                $logEvent->setProject(null);
            }
        }

        return $this;
    }

    public function getApplicantRating(): ?ProjectRateByApplicant
    {
        return $this->applicantRating;
    }

    public function setApplicantRating(?ProjectRateByApplicant $applicantRating): self
    {
        // unset the owning side of the relation if necessary
        if ($applicantRating === null && $this->applicantRating !== null) {
            $this->applicantRating->setProject(null);
        }

        // set the owning side of the relation if necessary
        if ($applicantRating !== null && $applicantRating->getProject() !== $this) {
            $applicantRating->setProject($this);
        }

        $this->applicantRating = $applicantRating;

        return $this;
    }

    public function getPubVideo(): ?string
    {
        return $this->pub_video;
    }

    public function setPubVideo(?string $pub_video): self
    {
        $this->pub_video = $pub_video;

        return $this;
    }

    public function getPubPresentation(): ?string
    {
        return $this->pub_presentation;
    }

    public function setPubPresentation(?string $pub_presentation): self
    {
        $this->pub_presentation = $pub_presentation;

        return $this;
    }

    public function getPubVideoStatus(): ?int
    {
        return $this->pub_video_status;
    }

    public function setPubVideoStatus(?int $pub_video_status): self
    {
        $this->pub_video_status = $pub_video_status;

        return $this;
    }
}
