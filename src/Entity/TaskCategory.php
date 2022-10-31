<?php

namespace App\Entity;

use App\Repository\TaskCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

//API PLATFORM
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TaskCategoryRepository::class)
 * @ApiResource(
 *     normalizationContext = {"groups" = {"tc_read:collection"} },
 *     collectionOperations = {"get"},
 *     itemOperations =  {
 *        "get" = {
 *           "normalization_context" =  {"groups" =  {"tc_read:collection", "tc_read:item"} }
 *         }
 *     }
 *)
 */
/*#[ApiResource(
    normalizationContext: ['groups' => ['tc_read:collection']],
    collectionOperations: ['get'],
    itemOperations: [
        'get' =>[
            'normalization_context' => ['groups' =>  ['tc_read:collection', 'tc_read:item']]
        ]
    ]
)]*/
class TaskCategory
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
     * @Assert\Length(min = 1, max = 255, minMessage="Vous devez utilisez {{limit}} caractère minimun.", maxMessage="Ne pas dépasser {{limit}} caractères.")
     * @Groups ({"tc_read:collection","t_read:item"})
     */
    //#[Groups(['tc_read:collection', 't_read:item'])]
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Veuillez renseigner une description")
     * @Assert\Length(min = 1, minMessage="Vous devez utilisez {{limit}} caractère minimun.")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="category")
     * @Groups ({"tc_read:item"})
     */
    //#[Groups(['tc_read:item'])]
    private $tasks;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
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
            $task->setCategory($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getCategory() === $this) {
                $task->setCategory(null);
            }
        }

        return $this;
    }
}
