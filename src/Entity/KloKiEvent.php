<?php

namespace App\Entity;

use App\DBAL\Types\HotelStateType;
use App\DBAL\Types\PressMaterialStateType;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

use Gedmo\Mapping\Annotation as Gedmo;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as MyAssert;

use App\DBAL\Types\ContractStateType;
use App\DBAL\Types\EventArtType;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\KloKiEventRepository")
 * @MyAssert\EventNoOverlap()
 * @MyAssert\EventNoChildChild()
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
     * @ORM\Column(name="art", type="EventArtType", nullable=false)
     * @DoctrineAssert\Enum(entity="App\DBAL\Types\EventArtType")
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
     * Note, that type of a field should be same as you set in Doctrine config
     * (in this case it is ContractStateType)
     *
     * @ORM\Column(name="contract_state", type="ContractStateType", nullable=false)
     * @DoctrineAssert\Enum(entity="App\DBAL\Types\ContractStateType")
     */
    private $contractState;


    /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime()
     * @Groups({"events:read"})
     */
    private $start;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $end;

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
     * @Assert\NotBlank()
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
     * @ORM\Column(type="boolean")
     * @Groups({"events:read"})
     * Fest oder nur optioniert?
     */
    private $isFixed;


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
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $stageOrder;


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
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="garderobeAtEvents")
     */
    private $helperGarderobe;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"events:read"})
     */
    private $allDay;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="eventsLicht")
     */
    private $LichtTechniker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="eventsTon")
     */
    private $TonTechniker;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is4HoursDeal;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isReducedPrice;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is4hPrice;

    /**
     * Note, that type of a field should be same as you set in Doctrine config
     * (in this case it is HotelStateType)
     *
     * @ORM\Column(type="HotelStateType", nullable=false)
     * @DoctrineAssert\Enum(entity="App\DBAL\Types\HotelStateType")
     */
    private $hotelState;

    /**
     * @ORM\Column(type="PressMaterialStateType")
     */
    private $pressMaterialState;

    /**
     * @ORM\Column(type="PressMaterialStateType")
     */
    private $gemaListState;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $overnightStays;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $contractNumber;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $contractRevision;


    public function __construct()
    {
        $this->Ausstattung      = new ArrayCollection();
        $this->childEvents      = new ArrayCollection();
        $this->availableHelpers = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getStart()->format('Y-m-d') . ' ' . $this->name . ' (' . $this->getRoom()->getName() . ')';
    }


    ////////////////////////////////////////////
    /// Setters
    ////////////////////////////////////////////

    public function setName($name):                                   self { $this->name = $name;                             return $this; }
    public function setArt($art):                                     self { $this->art = $art;                               return $this; }
    public function setKategorie(?KloKiEventKategorie $kategorie):    self { $this->kategorie = $kategorie;                   return $this; }
    public function setRoom(?Room $room):                             self { $this->room = $room;                             return $this; }
    public function setKontakt(?Addresse $kontakt):                   self { $this->kontakt = $kontakt;                       return $this; }
    public function setBestPlan(?Bestuhlungsplan $bestPlan):          self { $this->bestPlan = $bestPlan;                     return $this; }
    public function setStageOrder(?StageOrder $stageOrder):           self { $this->stageOrder = $stageOrder;                 return $this; }
    public function setHelperKasse(?User $helperKasse):               self { $this->helperKasse = $helperKasse;               return $this; }
    public function setHelperEinlassEins(?User $helperEinlassEins):   self { $this->helperEinlassEins = $helperEinlassEins;   return $this; }
    public function setHelperEinlassZwei(?User $helperEinlassZwei):   self { $this->helperEinlassZwei = $helperEinlassZwei;   return $this; }
    public function setHelperSpringerEins(?User $helperSpringerEins): self { $this->helperSpringerEins = $helperSpringerEins; return $this; }
    public function setHelperSpringerZwei(?User $helperSpringerZwei): self { $this->helperSpringerZwei = $helperSpringerZwei; return $this; }
    public function setStart($start):                                 self { $this->start = $start;                           return $this; }
    public function setEnd($end):                                     self { $this->end = $end;                               return $this; }
    public function setAnzahlArtists(?int $anzahlArtists):            self { $this->anzahlArtists = $anzahlArtists;           return $this; }
    public function setIsBestBenoetigt(bool $isBestBenoetigt):        self { $this->isBestBenoetigt = $isBestBenoetigt;       return $this; }
    public function setIsLichtBenoetigt(bool $isLichtBenoetigt):      self { $this->isLichtBenoetigt = $isLichtBenoetigt;     return $this; }
    public function setLichtTechniker(?User $LichtTechniker):         self { $this->LichtTechniker = $LichtTechniker;         return $this; }
    public function setTonTechniker(?User $TonTechniker):             self { $this->TonTechniker = $TonTechniker;             return $this; }
    public function setIsReducedPrice(bool $isReducedPrice):          self { $this->isReducedPrice = $isReducedPrice;         return $this; }
    public function setIs4hPrice(bool $is4hPrice):                    self { $this->is4hPrice = $is4hPrice;                   return $this; }
    public function setHelperGarderobe(?User $helperGarderobe):       self { $this->helperGarderobe = $helperGarderobe;       return $this; }
    public function setHotelState($hotelState):                       self { $this->hotelState = $hotelState;                 return $this; }
    public function setPressMaterialState($pressMaterialState):       self { $this->pressMaterialState = $pressMaterialState; return $this; }
    public function setGemaListState($gemaListState):                 self { $this->gemaListState = $gemaListState;           return $this; }
    public function setOvernightStays(?int $overnightStays):          self { $this->overnightStays = $overnightStays;         return $this; }
    public function setContractNumber(?int $contractNumber):          self { $this->contractNumber = $contractNumber;         return $this; }
    public function setContractRevision(?int $contractRevision):      self { $this->contractRevision = $contractRevision;     return $this; }
    public function setContractState($contractState):                 self { $this->contractState = $contractState;           return $this; }
    public function setParentEvent(?self $ParentEvent):               self { $this->ParentEvent = $ParentEvent;               return $this; }
    public function setBemerkung(?string $Bemerkung):                 self { $this->Bemerkung = $Bemerkung;                   return $this; }
    public function setIsFixed(bool $isFixed):                        self { $this->isFixed = $isFixed;                       return $this; }
    public function setIsTonBenoetigt(bool $isTonBenoetigt):          self { $this->isTonBenoetigt = $isTonBenoetigt;         return $this; }
    public function setHelperRequired(bool $helperRequired):          self { $this->helperRequired = $helperRequired;         return $this; }
    public function setAllDay(?bool $allDay):                         self { $this->allDay = $allDay;                         return $this; }

    public function set_FC_end($end) : self
    {
        // Bei allDay-Events liefert FullCalendar als Ende-Datum das Datum des
        // ersten Tages NACH dem Event. Wir haben aber das letzte Datum des Events in der Datenbank stehen!
        if($this->allDay)
            $this->end = $end->sub(new \DateInterval('P1D'));
        else
            $this->end = $end;
        return $this;
    }
/*
    public function setPleaseMakeContract(bool $s)
    {
        if($s && ($this->getContractState() == ContractStateType::NONE || $this->getContractState() == null))
        {
            $this->setContractState(ContractStateType::REQUESTED);
        }
        if((!$s) && ($this->getContractState() == ContractStateType::REQUESTED || $this->getContractState() == null))
        {
            $this->setContractState(ContractStateType::NONE);
        }
        return $this;
    }

    public function getPleaseMakeContract() : bool
    {
        return in_array($this->contractState, [ContractStateType::REQUESTED, ContractStateType::SENT, ContractStateType::RECEIVED]);
    }
*/



    /**
     * @param mixed $startDate
     */
    public function setStartDate($startDate): self
    {
        if($this->start)
            $this->start = new \DateTime($startDate->format('Y-m-d ') . $this->start->format('H:i'));
        else
            $this->start = $startDate;
        return $this;
    }


    /**
     * @param mixed $endDate
     */
    public function setEndDate($endDate): self
    {
        if($this->end)
            $this->end = new \DateTime($endDate->format('Y-m-d ') . $this->end->format('H:i'));
        else
            $this->end = $endDate;
        return $this;
    }


    /**
     * @param mixed $startTime
     */
    public function setStartTime($startTime): self
    {
        if($this->start && $startTime)
        {
            $this->start = new \DateTime($this->start->format('Y-m-d ') . $startTime->format('H:i'));
        }
        return $this;
    }

    /**
     * @param mixed $endTime
     */
    public function setEndTime($endTime): self
    {
        if($this->end)
        {
            if($endTime)
                $this->end = new \DateTime($this->end->format('Y-m-d ') . $endTime->format('H:i'));
            else
                $this->end = new \DateTime($this->end->format('Y-m-d ') . '23:30');
        }
        return $this;
    }


    //////////////////////////////////
    // Getter
    //////////////////////////////////

    // Boolean-Getters
    public function getIsReducedPrice():        ?bool   { return $this->isReducedPrice;     }
    public function getIs4hPrice():             ?bool   { return $this->is4hPrice;          }
    public function getAllDay():                ?bool   { return $this->allDay;             }
    public function getIsTonBenoetigt():        ?bool   { return $this->isTonBenoetigt;     }
    public function getHelperRequired():        ?bool   { return $this->helperRequired;     }
    public function getIsBestBenoetigt():       ?bool   { return $this->isBestBenoetigt;    }
    public function getIsLichtBenoetigt():      ?bool   { return $this->isLichtBenoetigt;   }
    public function getIsFixed():               ?bool   { return $this->isFixed;            }

    // Integer-Getters
    public function getId():                    ?int    { return $this->id;                 }
    public function getAnzahlArtists():         ?int    { return $this->anzahlArtists;      }
    public function getOvernightStays():        ?int    { return $this->overnightStays;     }
    public function getContractNumber():        ?int    { return $this->contractNumber;     }
    public function getContractRevision():      ?int    { return $this->contractRevision;   }

    // User-Getters
    public function getHelperGarderobe():       ?User   { return $this->helperGarderobe;    }
    public function getTonTechniker():          ?User   { return $this->TonTechniker;       }
    public function getLichtTechniker():        ?User   { return $this->LichtTechniker;     }
    public function getHelperEinlassEins():     ?User   { return $this->helperEinlassEins;  }
    public function getHelperEinlassZwei():     ?User   { return $this->helperEinlassZwei;  }
    public function getHelperKasse():           ?User   { return $this->helperKasse;        }
    public function getHelperSpringerEins():    ?User   { return $this->helperSpringerEins; }
    public function getHelperSpringerZwei():    ?User   { return $this->helperSpringerZwei; }
    public function getCreatedBy():             ?User   { return $this->createdBy;          }
    public function getUpdatedBy():             ?User   { return $this->updatedBy;          }

    // Enum-Getters
    public function getPressMaterialState()             { return $this->pressMaterialState; }
    public function getGemaListState()                  { return $this->gemaListState; }
    public function getHotelState()                     { return $this->hotelState;         }
    public function getContractState():         ?string { return $this->contractState;      }
    public function getArt()                            { return $this->art;                }

    // String-Getters
    public function getName():                  ?string { return $this->name;               }
    public function getBemerkung():             ?string { return $this->Bemerkung;          }

    // Entity-Getters
    public function getKategorie():   ?KloKiEventKategorie  { return $this->kategorie;    }
    public function getRoom():        ?Room                 { return $this->room;         }
    public function getKontakt():     ?Addresse             { return $this->kontakt;      }
    public function getBestPlan():    ?Bestuhlungsplan      { return $this->bestPlan;     }
    public function getStageOrder():  ?StageOrder           { return $this->stageOrder;   }
    public function getParentEvent(): ?self                 { return $this->ParentEvent;  }

    // Time-Getters
    public function getStart(): ?\DateTimeInterface         { return $this->start;        }
    public function getEnd():   ?\DateTimeInterface         { return $this->end;          }

    // Collections
    public function getChildEvents():      Collection    { return $this->childEvents;      }
    public function getAusstattung():      Collection    { return $this->Ausstattung;      }
    public function getAvailableHelpers(): Collection    { return $this->availableHelpers; }

    /**
     * @return mixed
     * @Assert\NotBlank()
     */
    public function getStartDate()
    {
        return $this->start;
    }

    /**
     * @return mixed
     */
    public function getStartTime()
    {
        return $this->start;
    }

    /**
     * @return mixed
     */
    public function getEndTime()
    {
        return $this->end;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->end;
    }


    /**
     * @Groups({"events:read"})
     */
    public function getResourceId()
    {
        return $this->getRoom()->getId();
    }


    /****************************
     * Price-Functions
     *****************************/

    /**
     * Gibt die Raummiete dieses Events zurück.
     * Dabei wird berücksichtigt, ob es ein 4h-Event ist und ob der
     * Kunde einen 10%-Rabatt bekommt.
     * Keine MwSt für Raummiete, also Brutto=Netto
     * @return int|null
     */
    public function getRoomFee() : ?int
    {
        $fullPrice = $this->is4hPrice ? $this->getRoom()->getHalfDayPrice() : $this->getRoom()->getFullDayPrice();
        return $this->getIsReducedPrice() ? $fullPrice * 0.9 : $fullPrice;
    }

    /**
     * Gibt die Summe der Raummiete dieses Events und aller Unter-Events zurück
     * @return int|null
     */
    public function getAllRoomFees() : ? int
    {
        $preis = $this->getRoomFee();
        foreach ($this->getChildEvents() as $e)
        {
            $preis += $e->getRoomFee();
        }
        return $preis;
    }

    /**
     * Liefert die Summe der Netto-Preise der Ausstattungen dieses Events
     * @return int
     */
    public function getAusstattungNetPreis() : int
    {
        $sum = 0;
        foreach($this->getAusstattung() as $a)
            $sum += $a->getNettopreis();
        return $sum;
    }

    /**
     * Liefert die Summe der Brutto-Preise der Ausstattung dieses Events
     * @return int
     */
    public function getAusstattungBruttoPreis() : int
    {
        $sum = 0;
        foreach($this->getAusstattung() as $a)
            $sum += $a->getBruttoPreis();
        return $sum;
    }

    /**
     * Liefert die Summe der Netto-Preise der Ausstattung dieses Events und aller Unterevents
     * @return int
     */
    public function getAllAusstattungNettoPreis() : int
    {
        $sum = $this->getAusstattungNetPreis();
        foreach ($this->getChildEvents() as $e)
            $sum += $e->getAusstattungNetPreis();
        return $sum;
    }

    /**
     * Liefert die Summe der Brutto-Preise der Ausstattung dieses Events und aller Unterevents
     * @return int
     */
    public function getAllAusstattungBruttoPreis() : int
    {
        $sum = $this->getAusstattungBruttoPreis();
        foreach ($this->getChildEvents() as $e)
            $sum += $e->getAusstattungBruttoPreis();
        return $sum;
    }

    /**
     * Liefert die anfallende MwSt. für dieses Event
     * (MwSt. fällt nur auf Ausstattung an, nicht auf Raummiete)
     * @return int|null
     */
    public function getMwst() : ? int
    {
        $mwst = 0;
        foreach ($this->getAusstattung() as $a)
        {
            $mwst += $a->getMwSt();
        }
        return $mwst;
    }


    /**
     * Liefert die anfallende MwSt. für dieses Event und alle Unterevents
     * (MwSt. fällt nur auf Ausstattung an, nicht auf Raummiete)
     * @return int|null
     */
    public function getAllMwSt() : int
    {
        $sum = $this->getMwst();
        foreach($this->getChildEvents() as $e)
            $sum += $e->getMwst();
        return $sum;
    }

    /**
     * Liefert die Summe der Raummiete und
     * der Brutto-Preise aller Ausstattungen
     * dieses Events
     * @return int|null
     */
    public function getBruttoPreis() : ? int
    {
        $preis = $this->getRoomFee();
        $preis += $this->getAusstattungBruttoPreis();
        return $preis;
    }

    /**
     * Liefert die Summe der Raummiete und
     * der Brutto-Preise aller Ausstattungen
     * dieses Events und aller Unterevents
     * @return int|null
     */
    public function getAllBruttoPreis() : ? int
    {
        $preis = $this->getRoomFee();
        $preis += $this->getAusstattungBruttoPreis();
        foreach ($this->getChildEvents() as $e)
        {
            $preis += $e->getBruttoPreis();
        }
        return $preis;
    }

    /**
     * Liefert die Summe der Netto-Preise dieses Events
     * und aller Unter-Events zurück
     * @return int|null
     */
    public function getNettoPreis() : ? int
    {
        $preis = $this->getRoomFee();
        foreach ($this->getAusstattung() as $a)
        {
            $preis += $a->getNettoPreis();
        }
        return $preis;
    }

    /**
     * Liefert die Summe der Netto-Preise dieses Events
     * und aller Unter-Events zurück
     * @return int|null
     */
    public function getAllNettoPreis() : ? int
    {
        $preis = $this->getRoomFee();
        foreach ($this->getAusstattung() as $a)
        {
            $preis += $a->getNettoPreis();
        }
        foreach ($this->getChildEvents() as $e)
        {
            $preis += $e->getNettoPreis();
        }
        return $preis;
    }

    /**
     * @Groups({"events:read"})
     * @SerializedName("end")
     */
    public function getCorrectedEnd() : ?\DateTimeInterface
    {
        // Bei allDay-Events erwartet FullCalendar als Ende-Datum das Datum des
        // ersten Tages NACH dem Event. Wir haben aber das letzte Datum des Events in der Datenbank stehen!
        //
        if($this->allDay && $this->end)
            return $this->end->add(new \DateInterval('P1D'));
        else
            return $this->end;
    }


    public function getProblemDetails() : array
    {
        $problems = array();
        if($this->isTonBenoetigt && ($this->TonTechniker === null))
        {
            $problems[] = "Ton-Techniker fehlt";
        }
        if($this->isLichtBenoetigt && ($this->LichtTechniker === null))
        {
            $problems[] = "Licht-Techniker fehlt";
        }
        if($this->helperRequired)
        {
            $unassignedFunction = [];
            if(!$this->helperEinlassEins)  $unassignedFunction[] = 'Einlass 1';
            if(!$this->helperEinlassZwei)  $unassignedFunction[] = 'Einlass 2';
            if(!$this->helperKasse)        $unassignedFunction[] = 'Kasse';
            if(!$this->helperSpringerEins) $unassignedFunction[] = 'Springer 1';
            if(!$this->helperSpringerZwei) $unassignedFunction[] = 'Springer 2';
            if(!$this->helperGarderobe)    $unassignedFunction[] = 'Garderobe';
            if($unassignedFunction) $problems[] = "Helfer fehlt: " . implode(", ", $unassignedFunction);

            $unconfirmedHelpers = [];
            if($this->helperEinlassEins  && (!$this->availableHelpers->contains($this->helperEinlassEins))  && (!in_array($this->helperEinlassEins,  $unconfirmedHelpers)))
                $unconfirmedHelpers[] = $this->helperEinlassEins;
            if($this->helperEinlassZwei  && (!$this->availableHelpers->contains($this->helperEinlassZwei))  && (!in_array($this->helperEinlassZwei,  $unconfirmedHelpers)))
                $unconfirmedHelpers[] = $this->helperEinlassZwei;
            if($this->helperKasse        && (!$this->availableHelpers->contains($this->helperKasse))        && (!in_array($this->helperKasse,        $unconfirmedHelpers)))
                $unconfirmedHelpers[] = $this->helperKasse;
            if($this->helperSpringerEins && (!$this->availableHelpers->contains($this->helperSpringerEins)) && (!in_array($this->helperSpringerEins, $unconfirmedHelpers)))
                $unconfirmedHelpers[] = $this->helperSpringerEins;
            if($this->helperSpringerZwei && (!$this->availableHelpers->contains($this->helperSpringerZwei)) && (!in_array($this->helperSpringerZwei, $unconfirmedHelpers)))
                $unconfirmedHelpers[] = $this->helperSpringerZwei;
            if($this->helperGarderobe && (!$this->availableHelpers->contains($this->helperGarderobe)) && (!in_array($this->helperGarderobe, $unconfirmedHelpers)))
                $unconfirmedHelpers[] = $this->helperGarderobe;
            if($unconfirmedHelpers) $problems[] = "Zusage fehlt: " .  implode(", ", $unconfirmedHelpers);

        }
        if($this->isBestBenoetigt && (!$this->bestPlan))
        {
            $problems[] = "Kein Bestuhlungsplan ausgewählt";
        }

        if($this->hotelState == HotelStateType::NEEDED)
        {
            $problems[] = "Hotel muss noch gebucht werden!";
        }

        if($this->pressMaterialState == PressMaterialStateType::NEEDED)
        {
            $problems[] = "Pressematerial muss noch besorgt werden!";
        }
        return $problems;
    }

    //////////////////////////////////
    /// Adders
    //////////////////////////////////

    public function addAusstattung(Ausstattung $ausstattung): self
    {
        if (!$this->Ausstattung->contains($ausstattung)) {
            $this->Ausstattung[] = $ausstattung;
        }
        return $this;
    }

    public function addChildEvent(self $childEvent): self
    {
        if (!$this->childEvents->contains($childEvent)) {
            $this->childEvents[] = $childEvent;
            $childEvent->setParentEvent($this);
        }
        return $this;
    }

    public function addAvailableHelper(User $availableHelper): self
    {
        if (!$this->availableHelpers->contains($availableHelper)) {
            $this->availableHelpers[] = $availableHelper;
        }
        return $this;
    }

    //////////////////////////////////
    /// Removers
    //////////////////////////////////
    public function removeAusstattung(Ausstattung $ausstattung): self
    {
        if ($this->Ausstattung->contains($ausstattung)) {
            $this->Ausstattung->removeElement($ausstattung);
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

    public function removeAvailableHelper(User $availableHelper): self
    {
        if ($this->availableHelpers->contains($availableHelper)) {
            $this->availableHelpers->removeElement($availableHelper);
        }
        return $this;
    }







}