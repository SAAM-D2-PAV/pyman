<?php

namespace App\Entity;

//use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

use App\Repository\NoteRepository;
use Doctrine\ORM\Mapping as ORM;

//ATTRIBUTS (configurer doctrine.yaml)

/* #[ORM\Entity(repositoryClass: NoteRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    collectionOperations: ['get' => ['normalization_context' => ['groups' => 'n_read:collection']]],
    itemOperations: ['get' => ['normalization_context' => ['groups' => 'n_read:item']]],
    order: ['createdAt' => 'DESC']
)] */

// OU ANNOTATIONS

/**
 * @ORM\Entity(repositoryClass=NoteRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @ApiResource(
 *     normalizationContext = {"groups" = {"n_read:collection"} }, 
 *     collectionOperations = {"get"},
 *     itemOperations =  { "get" = { "normalization_context" =  { "groups" =  { "n_read:collection", "n_read:item" } } } },
 * )
 */
class Note
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups ({"n_read:collection"})
     */
    // #[Groups(['n_read:collection'])]
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"n_read:collection","n_read:item"})
     */
    // #[Groups(['n_read:collection', 'n_read:item'])]
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Groups({"n_read:item"})
     */
    // #[Groups(['n_read:item'])]
    private $description;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"n_read:collection","n_read:item"})
     */
    // #[Groups(['n_read:collection', 'n_read:item'])]
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"n_read:collection","n_read:item"})
     */
    // #[Groups(['n_read:collection', 'n_read:item'])]
    private $createdBy;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"n_read:collection","n_read:item"})
     */
    // #[Groups(['n_read:collection', 'n_read:item'])]
    private $allowUpdateFromAll;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): self
    {
        $this->createdAt = new \DateTimeImmutable();

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

    public function isAllowUpdateFromAll(): ?bool
    {
        return $this->allowUpdateFromAll;
    }

    public function setAllowUpdateFromAll(bool $allowUpdateFromAll): self
    {
        $this->allowUpdateFromAll = $allowUpdateFromAll;

        return $this;
    }
}
