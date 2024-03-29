<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RoomRepository")
 */
class Room
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"room:read", "resource:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"room:read", "events:read"})
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\KloKiEvent", mappedBy="room")
     * @Groups({"room:read"})
     */
    private $kloKiEvents;

    /**
     * @ORM\Column(type="string", length=7, nullable=true)
     * @Groups({"room:read", "events:read"})
     */
    private $color;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $fullDayPrice;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $halfDayPrice;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $fullDayPriceIntern;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $halfDayPriceIntern;


    /**
    * @Groups({"room:read", "resource:read"})
    */
    public function getTitle()
    {
        return $this->getName();
    }


    /**
     * @Groups({"room:read", "resource:read"})
     */
    public function getEventColor()
    {
        return $this->getColor();
    }


    public function __construct()
    {
        $this->kloKiEvents = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }


    /**
     * @return Collection|KloKiEvent[]
     */
    public function getKloKiEvents(): Collection
    {
        return $this->kloKiEvents;
    }

    public function addKloKiEvent(KloKiEvent $kloKiEvent): self
    {
        if (!$this->kloKiEvents->contains($kloKiEvent)) {
            $this->kloKiEvents[] = $kloKiEvent;
            $kloKiEvent->setRoom($this);
        }

        return $this;
    }

    public function removeKloKiEvent(KloKiEvent $kloKiEvent): self
    {
        if ($this->kloKiEvents->contains($kloKiEvent)) {
            $this->kloKiEvents->removeElement($kloKiEvent);
            // set the owning side to null (unless already changed)
            if ($kloKiEvent->getRoom() === $this) {
                $kloKiEvent->setRoom(null);
            }
        }

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getFullDayPrice(): ?int
    {
        return $this->fullDayPrice;
    }

    public function setFullDayPrice(?int $fullDayPrice): self
    {
        $this->fullDayPrice = $fullDayPrice;

        return $this;
    }

    public function getHalfDayPrice(): ?int
    {
        return $this->halfDayPrice;
    }

    public function setHalfDayPrice(?int $halfDayPrice): self
    {
        $this->halfDayPrice = $halfDayPrice;

        return $this;
    }
    public function getFullDayPriceIntern(): ?int
    {
        return $this->fullDayPriceIntern;
    }

    public function setFullDayPriceIntern(?int $fullDayPriceIntern): self
    {
        $this->fullDayPriceIntern = $fullDayPriceIntern;

        return $this;
    }

    public function getHalfDayPriceIntern(): ?int
    {
        return $this->halfDayPriceIntern;
    }

    public function setHalfDayPriceIntern(?int $halfDayPriceIntern): self
    {
        $this->halfDayPriceIntern = $halfDayPriceIntern;

        return $this;
    }
}
