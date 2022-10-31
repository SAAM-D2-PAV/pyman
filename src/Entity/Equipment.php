<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\EquipmentRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

//API PLATFORM
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiFilter;
/**
 * @ORM\Entity(repositoryClass=EquipmentRepository::class)
 * @ApiResource(
 *     normalizationContext = {"groups" = {"e_read:collection"} },
 *     collectionOperations = {"get"},
 *     itemOperations =  {
 *        "get" = {
 *           "normalization_context" =  {"groups" =  {"e_read:collection", "e_read:item"} }
 *         }
 *     }
 *)
 * @ApiFilter(SearchFilter::class, properties = {"identificationCode" = "exact", "name" = "partial"})
 */
class Equipment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner le nom du matériel")
     * @Assert\Length(min = 1, max = 255, minMessage="Vous devez utilisez {{  limit  }} caractère minimun.", maxMessage="Ne pas dépasser {{  limit  }} caractères.")
     * @Groups ({"e_read:collection"},{"e_read:item"})
     */
    //#[Groups(['e_read:collection', 't_read:item'])]
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner la référence du matériel")
     * @Assert\Length(min = 1, max = 255, minMessage="Vous devez utilisez {{  limit  }} caractère minimun.", maxMessage="Ne pas dépasser {{  limit  }} caractères.")
     * @Groups ({"e_read:item"},{"t_read:item"})
     */
    private $ref;

    // @Assert\NotBlank(message="Veuillez renseigner le modèle")
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min = 1, max = 255, minMessage="Vous devez utilisez {{  limit  }} caractère minimun.", maxMessage="Ne pas dépasser {{  limit  }} caractères.")
     */
    private $model;

    // @Assert\NotBlank(message="Veuillez renseigner la marque")
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min = 1, max = 255, minMessage="Vous devez utilisez {{ limit }} caractère minimun.", maxMessage="Ne pas dépasser {{ limit }} caractères.")
     */
    private $brand;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(min = 1, max = 255, minMessage="Vous devez utilisez {{ limit }} caractère minimun.", maxMessage="Ne pas dépasser {{ limit }} caractères.")
     * @Groups ({"e_read:item"},{"t_read:item"})
     */
    private $serialNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(min = 1, max = 255, minMessage="Vous devez utilisez {{ limit }} caractère minimun.", maxMessage="Ne pas dépasser {{ limit }} caractères.")
     * @Groups ({"e_read:item"},{"t_read:item"})
     */
    private $identificationCode;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner l'état du matériel")
     * @Assert\Length(min = 4, max = 255, minMessage="Vous devez utilisez {{ limit }} caractère minimun.", maxMessage="Ne pas dépasser {{ limit }} caractères.")
     */
    private $status;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(min = 4, max = 1000, minMessage="Vous devez utilisez {{ limit }} caractère minimun.", maxMessage="Ne pas dépasser {{ limit }} caractères.")
     * @Assert\Url(
     *    message = "L'url n'est pas valide",
     *    protocols = {"http", "https", "ftp"}
     * )
     */
    private $specifications;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity=EquipmentType::class, inversedBy="equipments", fetch="EAGER")
     * @Assert\NotBlank(message="Veuillez selectionner le type du matériel")
     */
    private $equipmentType;

    // @Assert\NotBlank(message="Veuillez ajouter une image")
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Url(message="merci de renseigner une url correcte")
     */
    private $mainPicture;

    /**
     * @ORM\ManyToMany(targetEntity=EquipmentCategory::class, inversedBy="equipment", fetch="EAGER")
     * @Assert\NotBlank(message="Veuillez selectionner les différentes catégories du matériel")
     */
    private $equipmentCategories;

    /**
     * @ORM\ManyToMany(targetEntity=Task::class, mappedBy="equipment")
     */
    private $tasks;

    /**
     * @ORM\ManyToOne(targetEntity=Location::class, inversedBy="equipment")
     * @Assert\NotBlank(message="Veuillez selectionner un lieu")
     */
    private $location;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $missing;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $note;

    /**
     * @ORM\OneToMany(targetEntity=Document::class, mappedBy="Equipment")
     */
    private $documents;

    /**
     * @ORM\ManyToOne(targetEntity=RentedEquipment::class, inversedBy="equipment")
     */
    private $rent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $uploadName;

    public function __construct()
    {
        $this->equipmentCategories = new ArrayCollection();
        $this->tasks = new ArrayCollection();
        $this->documents = new ArrayCollection();
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

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(string $ref): self
    {
        $this->ref = $ref;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getSerialNumber(): ?string
    {
        return $this->serialNumber;
    }

    public function setSerialNumber(?string $serialNumber): self
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    public function getIdentificationCode(): ?string
    {
        return $this->identificationCode;
    }

    public function setIdentificationCode(?string $identificationCode): self
    {
        $this->identificationCode = $identificationCode;

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

    public function getSpecifications(): ?string
    {
        return $this->specifications;
    }

    public function setSpecifications(?string $specifications): self
    {
        $this->specifications = $specifications;

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

    public function getEquipmentType(): ?EquipmentType
    {
        return $this->equipmentType;
    }

    public function setEquipmentType(?EquipmentType $equipmentType): self
    {
        $this->equipmentType = $equipmentType;

        return $this;
    }

    public function getMainPicture(): ?string
    {
        return $this->mainPicture;
    }

    public function setMainPicture(string $mainPicture): self
    {
        $this->mainPicture = $mainPicture;

        return $this;
    }

    /**
     * @return Collection|EquipmentCategory[]
     */
    public function getEquipmentCategories(): Collection
    {
        return $this->equipmentCategories;
    }

    public function addEquipmentCategory(EquipmentCategory $equipmentCategory): self
    {
        if (!$this->equipmentCategories->contains($equipmentCategory)) {
            $this->equipmentCategories[] = $equipmentCategory;
            $equipmentCategory->addEquipment($this);
        }
        return $this;
    }

    public function removeEquipmentCategory(EquipmentCategory $equipmentCategory): self
    {
        if ($this->equipmentCategories->removeElement($equipmentCategory)) {
            $equipmentCategory->removeEquipment($this);
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
            $task->addEquipment($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            $task->removeEquipment($this);
        }

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getMissing(): ?int
    {
        return $this->missing;
    }

    public function setMissing(?int $missing): self
    {
        $this->missing = $missing;

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
     * @return Collection<int, Document>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setEquipment($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getEquipment() === $this) {
                $document->setEquipment(null);
            }
        }

        return $this;
    }

    public function getRent(): ?RentedEquipment
    {
        return $this->rent;
    }

    public function setRent(?RentedEquipment $rent): self
    {
        $this->rent = $rent;

        return $this;
    }

    public function getUploadName(): ?string
    {
        return $this->uploadName;
    }

    public function setUploadName(?string $uploadName): self
    {
        $this->uploadName = $uploadName;

        return $this;
    }
}
