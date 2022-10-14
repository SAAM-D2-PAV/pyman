<?php

namespace App\Entity;

use App\Repository\EquipmentCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=EquipmentCategoryRepository::class)
 */
class EquipmentCategory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner le nom de la catégorie")
     * @Assert\Length(min = 1, max = 255, minMessage="Vous devez utilisez {{  limit  }} caractère minimun.", maxMessage="Ne pas dépasser {{  limit  }} caractères.")
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(min = 5, max = 255, minMessage="Vous devez utilisez {{  limit  }} caractère minimun.", maxMessage="Ne pas dépasser {{  limit  }} caractères.")
     */
    private $information;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\ManyToMany(targetEntity=Equipment::class, mappedBy="equipmentCategories", fetch="EAGER")
     */
    private $equipment;

    public function __construct()
    {
        $this->equipment = new ArrayCollection();
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

    public function getInformation(): ?string
    {
        return $this->information;
    }

    public function setInformation(?string $information): self
    {
        $this->information = $information;

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
            $equipment->addEquipmentCategory($this);
        }

        return $this;
    }

    public function removeEquipment(Equipment $equipment): self
    {
        $this->equipment->removeElement($equipment);

        return $this;
    }
}
