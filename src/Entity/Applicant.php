<?php

namespace App\Entity;

use App\Repository\ApplicantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass=ApplicantRepository::class)
 */
class Applicant
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner le prénom du contact")
     * @Assert\Length(min = 1, max = 255, minMessage="Vous devez utilisez {{ limit }} caractère minimun.", maxMessage="Ne pas dépasser {{ limit }} caractères.")
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner le nom du contact")
     * @Assert\Length(min = 1, max = 255, minMessage="Vous devez utilisez {{ limit }} caractère minimun.", maxMessage="Ne pas dépasser {{ limit }} caractères.")
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner le mail du contact")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $office;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner la fonction du contact")
     */
    private $job;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner la direction / département du contact")
     */
    private $department;

    /**
     * @ORM\OneToMany(targetEntity=Project::class, mappedBy="requestBy")
     */
    private $projects;

    /**
     * @ORM\OneToMany(targetEntity=ProjectRateByApplicant::class, mappedBy="applicant")
     */
    private $projectRated;

    /**
     * @ORM\OneToMany(targetEntity=LogEvent::class, mappedBy="ratedBy")
     */
    private $logEvents;

    /**
     * @ORM\OneToMany(targetEntity=RentedEquipment::class, mappedBy="applicant")
     */
    private $rent;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
        $this->projectRated = new ArrayCollection();
        $this->logEvents = new ArrayCollection();
        $this->rent = new ArrayCollection();
    }

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

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

    public function setDepartment(string $department): self
    {
        $this->department = $department;

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
            $project->setRequestBy($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getRequestBy() === $this) {
                $project->setRequestBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProjectRateByApplicant[]
     */
    public function getProjectRated(): Collection
    {
        return $this->projectRated;
    }

    public function addProjectRated(ProjectRateByApplicant $projectRated): self
    {
        if (!$this->projectRated->contains($projectRated)) {
            $this->projectRated[] = $projectRated;
            $projectRated->setApplicant($this);
        }

        return $this;
    }

    public function removeProjectRated(ProjectRateByApplicant $projectRated): self
    {
        if ($this->projectRated->removeElement($projectRated)) {
            // set the owning side to null (unless already changed)
            if ($projectRated->getApplicant() === $this) {
                $projectRated->setApplicant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LogEvent>
     */
    public function getLogEvents(): Collection
    {
        return $this->logEvents;
    }

    public function addLogEvent(LogEvent $logEvent): self
    {
        if (!$this->logEvents->contains($logEvent)) {
            $this->logEvents[] = $logEvent;
            $logEvent->setRatedBy($this);
        }

        return $this;
    }

    public function removeLogEvent(LogEvent $logEvent): self
    {
        if ($this->logEvents->removeElement($logEvent)) {
            // set the owning side to null (unless already changed)
            if ($logEvent->getRatedBy() === $this) {
                $logEvent->setRatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RentedEquipment>
     */
    public function getRent(): Collection
    {
        return $this->rent;
    }

    public function addRent(RentedEquipment $rent): self
    {
        if (!$this->rent->contains($rent)) {
            $this->rent[] = $rent;
            $rent->setApplicant($this);
        }

        return $this;
    }

    public function removeRent(RentedEquipment $rent): self
    {
        if ($this->rent->removeElement($rent)) {
            // set the owning side to null (unless already changed)
            if ($rent->getApplicant() === $this) {
                $rent->setApplicant(null);
            }
        }

        return $this;
    }
}
