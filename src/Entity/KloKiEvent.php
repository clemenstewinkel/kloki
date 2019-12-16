<?php

namespace App\Entity;

use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;

use Gedmo\Mapping\Annotation as Gedmo;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as MyAssert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\KloKiEventRepository")
 * @MyAssert\EventNoOverlap()
 */
class KloKiEvent
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"events:read", "room:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"events:read", "room:read"})
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\KloKiEventType", inversedBy="kloKiEvents")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"events:read"})
     */
    private $art;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\KloKiEventKategorie", inversedBy="kloKiEvents")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"events:read"})
     */
    private $kategorie;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime()
     * @Groups({"events:read"})
     */
    private $beginAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime()
     * @Groups({"events:read"})
     */
    private $endAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\PositiveOrZero()
     * @Groups({"events:read"})
     * Wieviele Künstler stehen auf der Bühne
     */
    private $anzahlArtists;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Addresse", inversedBy="kloKiEvents")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Type(type="App\Entity\Addresse")
     * @Assert\Valid
     * @Groups({"events:read"})
     */
    private $kontakt;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"events:read"})
     * Ist ein Bestuhlungsplan nötig
     */
    private $isBestBenoetigt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bestuhlungsplan", inversedBy="kloKiEvents")
     * @Groups({"events:read"})
     * Verweis auf Bestuhlungsplan
     */
    private $bestPlan;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"events:read"})
     */
    private $isLichtBenoetigt;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Ausstattung", inversedBy="kloKiEvents")
     * @Groups({"events:read"})
     */
    private $Ausstattung;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Room", inversedBy="kloKiEvents")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"events:read", "room:read"})
     */
    private $room;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\KloKiEvent", inversedBy="childEvents")
     * @Groups({"events:read"})
     */
    private $ParentEvent;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\KloKiEvent", mappedBy="ParentEvent")
     */
    private $childEvents;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"events:read"})
     */
    private $Bemerkung;


    /**
     * @var User $createdBy
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * @Groups({"events:read"})
     */
    private $createdBy;

    /**
     * @var User $updatedBy
     *
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     * @Groups({"events:read"})
     */
    private $updatedBy;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\StageOrder", inversedBy="kloKiEvents")
     */
    private $stageOrder;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"events:read"})
     */
    private $isFixed;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"events:read"})
     */
    private $isTonBenoetigt;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="kloKiEvents")
     * @Groups({"events:read"})
     */
    private $availableHelpers;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"events:read"})
     */
    private $helperRequired;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="einlassEinsAtEvents")
     * @Groups({"events:read"})
     */
    private $helperEinlassEins;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="einlassZweiAtEvents")
     * @Groups({"events:read"})
     */
    private $helperEinlassZwei;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="kasseAtEvents")
     * @Groups({"events:read"})
     */
    private $helperKasse;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="springerEinsAtEvents")
     * @Groups({"events:read"})
     */
    private $helperSpringerEins;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="springerZweiAtEvents")
     * @Groups({"events:read"})
     */
    private $helperSpringerZwei;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isFullDay;

    // Fields without ORM
    private $startDate;
    private $endDate;
    private $startTime;
    private $endTime;


    /**
     * @Groups({"events:read"})
     */
    public function getResourceId()
    {
        return $this->getRoom()->getId();
    }




    public function __construct()
    {
        $this->Ausstattung = new ArrayCollection();
        $this->childEvents = new ArrayCollection();
        $this->availableHelpers = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name . ', ' . $this->getRoom()->getName() . ', ' . $this->getBeginAt()->format('Y-m-d H:i') ;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getArt(): ?KloKiEventType
    {
        return $this->art;
    }

    public function setArt(?KloKiEventType $art): self
    {
        $this->art = $art;

        return $this;
    }

    public function getKategorie(): ?KloKiEventKategorie
    {
        return $this->kategorie;
    }

    public function setKategorie(?KloKiEventKategorie $kategorie): self
    {
        $this->kategorie = $kategorie;

        return $this;
    }
    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): self
    {
        $this->room = $room;

        return $this;
    }

    public function getKontakt(): ?Addresse
    {
        return $this->kontakt;
    }

    public function setKontakt(?Addresse $kontakt): self
    {
        $this->kontakt = $kontakt;

        return $this;
    }
    public function getBestPlan(): ?Bestuhlungsplan
    {
        return $this->bestPlan;
    }

    public function setBestPlan(?Bestuhlungsplan $bestPlan): self
    {
        $this->bestPlan = $bestPlan;

        return $this;
    }

    public function getStageOrder(): ?StageOrder
    {
        return $this->stageOrder;
    }

    public function setStageOrder(?StageOrder $stageOrder): self
    {
        $this->stageOrder = $stageOrder;

        return $this;
    }

    public function getHelperEinlassEins(): ?User
    {
        return $this->helperEinlassEins;
    }

    public function setHelperEinlassEins(?User $helperEinlassEins): self
    {
        $this->helperEinlassEins = $helperEinlassEins;

        return $this;
    }

    public function getHelperEinlassZwei(): ?User
    {
        return $this->helperEinlassZwei;
    }

    public function setHelperEinlassZwei(?User $helperEinlassZwei): self
    {
        $this->helperEinlassZwei = $helperEinlassZwei;

        return $this;
    }

    public function getHelperKasse(): ?User
    {
        return $this->helperKasse;
    }

    public function setHelperKasse(?User $helperKasse): self
    {
        $this->helperKasse = $helperKasse;

        return $this;
    }

    public function getHelperSpringerEins(): ?User
    {
        return $this->helperSpringerEins;
    }

    public function setHelperSpringerEins(?User $helperSpringerEins): self
    {
        $this->helperSpringerEins = $helperSpringerEins;

        return $this;
    }

    public function getHelperSpringerZwei(): ?User
    {
        return $this->helperSpringerZwei;
    }

    public function setHelperSpringerZwei(?User $helperSpringerZwei): self
    {
        $this->helperSpringerZwei = $helperSpringerZwei;

        return $this;
    }
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    public function getBeginAt(): ?\DateTimeInterface
    {
        return $this->beginAt;
    }

    public function setBeginAt($beginAt): self
    {
        $this->beginAt = $beginAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt($endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getAnzahlArtists(): ?int
    {
        return $this->anzahlArtists;
    }

    public function setAnzahlArtists(?int $anzahlArtists): self
    {
        $this->anzahlArtists = $anzahlArtists;

        return $this;
    }

    public function getIsBestBenoetigt(): ?bool
    {
        return $this->isBestBenoetigt;
    }

    public function setIsBestBenoetigt(bool $isBestBenoetigt): self
    {
        $this->isBestBenoetigt = $isBestBenoetigt;

        return $this;
    }

    public function getIsLichtBenoetigt(): ?bool
    {
        return $this->isLichtBenoetigt;
    }

    public function setIsLichtBenoetigt(bool $isLichtBenoetigt): self
    {
        $this->isLichtBenoetigt = $isLichtBenoetigt;

        return $this;
    }

    /**
     * @return Collection|Ausstattung[]
     */
    public function getAusstattung(): Collection
    {
        return $this->Ausstattung;
    }

    public function addAusstattung(Ausstattung $ausstattung): self
    {
        if (!$this->Ausstattung->contains($ausstattung)) {
            $this->Ausstattung[] = $ausstattung;
        }

        return $this;
    }

    public function removeAusstattung(Ausstattung $ausstattung): self
    {
        if ($this->Ausstattung->contains($ausstattung)) {
            $this->Ausstattung->removeElement($ausstattung);
        }

        return $this;
    }

    public function getParentEvent(): ?self
    {
        return $this->ParentEvent;
    }

    public function setParentEvent(?self $ParentEvent): self
    {
        $this->ParentEvent = $ParentEvent;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getChildEvents(): Collection
    {
        return $this->childEvents;
    }

    public function addChildEvent(self $childEvent): self
    {
        if (!$this->childEvents->contains($childEvent)) {
            $this->childEvents[] = $childEvent;
            $childEvent->setParentEvent($this);
        }

        return $this;
    }

    public function removeChildEvent(self $childEvent): self
    {
        if ($this->childEvents->contains($childEvent)) {
            $this->childEvents->removeElement($childEvent);
            // set the owning side to null (unless already changed)
            if ($childEvent->getParentEvent() === $this) {
                $childEvent->setParentEvent(null);
            }
        }

        return $this;
    }

    public function getBemerkung(): ?string
    {
        return $this->Bemerkung;
    }

    public function setBemerkung(?string $Bemerkung): self
    {
        $this->Bemerkung = $Bemerkung;

        return $this;
    }


    public function getIsFixed(): ?bool
    {
        return $this->isFixed;
    }

    public function setIsFixed(bool $isFixed): self
    {
        $this->isFixed = $isFixed;

        return $this;
    }

    public function getIsTonBenoetigt(): ?bool
    {
        return $this->isTonBenoetigt;
    }

    public function setIsTonBenoetigt(bool $isTonBenoetigt): self
    {
        $this->isTonBenoetigt = $isTonBenoetigt;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getAvailableHelpers(): Collection
    {
        return $this->availableHelpers;
    }

    public function addAvailableHelper(User $availableHelper): self
    {
        if (!$this->availableHelpers->contains($availableHelper)) {
            $this->availableHelpers[] = $availableHelper;
        }

        return $this;
    }

    public function removeAvailableHelper(User $availableHelper): self
    {
        if ($this->availableHelpers->contains($availableHelper)) {
            $this->availableHelpers->removeElement($availableHelper);
        }

        return $this;
    }

    public function getHelperRequired(): ?bool
    {
        return $this->helperRequired;
    }

    public function setHelperRequired(bool $helperRequired): self
    {
        $this->helperRequired = $helperRequired;

        return $this;
    }

    public function getIsFullDay(): ?bool
    {
        return $this->isFullDay;
    }

    public function setIsFullDay(?bool $isFullDay): self
    {
        $this->isFullDay = $isFullDay;

        return $this;
    }

    /**
     * @return mixed
     * @Assert\NotBlank()
     */
    public function getStartDate()
    {
        return $this->beginAt;
    }

    /**
     * @param mixed $startDate
     */
    public function setStartDate($startDate): void
    {
        $this->startDate = $startDate;
        $this->beginAt = $startDate;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endAt;
    }

    /**
     * @param mixed $endDate
     */
    public function setEndDate($endDate): void
    {
        $this->endDate = $endDate;
        $this->endAt = $endDate;
    }

    /**
     * @return mixed
     */
    public function getStartTime()
    {
        return $this->beginAt;
    }

    /**
     * @param mixed $startTime
     */
    public function setStartTime($startTime): void
    {
        $this->startTime = $startTime;
        if($this->startDate && (!$this->isFullDay) && $startTime)
        {
            $this->beginAt = new \DateTime($this->startDate->format('Y-m-d ') . $startTime->format('H:i'));
        }
    }

    /**
     * @return mixed
     */
    public function getEndTime()
    {
        return $this->endAt;
    }

    /**
     * @param mixed $endTime
     */
    public function setEndTime($endTime): void
    {
        $this->endTime = $endTime;
        if($this->endDate && (!$this->isFullDay) && $endTime)
        {
            $this->endAt = new \DateTime($this->endDate->format('Y-m-d ') . $endTime->format('H:i'));
        }
    }

}
