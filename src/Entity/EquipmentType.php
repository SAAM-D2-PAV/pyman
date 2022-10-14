<?php

namespace App\Entity;

use App\Repository\EquipmentTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=EquipmentTypeRepository::class)
 */
class EquipmentType
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner le nom du type de matériel")
     * @Assert\Length(min = 1, max = 255, minMessage="Vous devez utilisez {{  limit  }} caractère minimun.", maxMessage="Ne pas dépasser {{  limit  }} caractères.")
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(min = 5, max = 255, minMessage="Vous devez utilisez {{  limit  }} caractère minimun.", maxMessage="Ne pas dépasser {{  limit  }} caractères.")
     */
    private $information;

    /**
     * @ORM\OneToMany(targetEntity=Equipment::class, mappedBy="equipmentType")
     */
    private $equipments;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    public function __construct()
    {
        $this->equipments = new ArrayCollection();
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

    /**
     * @return Collection|Equipment[]
     */
    public function getEquipments(): Collection
    {
        return $this->equipments;
    }

    public function addEquipment(Equipment $equipment): self
    {
        if (!$this->equipments->contains($equipment)) {
            $this->equipments[] = $equipment;
            $equipment->setEquipmentType($this);
        }

        return $this;
    }

    public function removeEquipment(Equipment $equipment): self
    {
        if ($this->equipments->removeElement($equipment)) {
            // set the owning side to null (unless already changed)
            if ($equipment->getEquipmentType() === $this) {
                $equipment->setEquipmentType(null);
            }
        }

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
}
