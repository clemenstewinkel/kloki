<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AusstattungRepository")
 */
class Ausstattung
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $nettopreis;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\KloKiEvent", mappedBy="Ausstattung")
    private $kloKiEvents;
     */

    public function __toString()
    {
        return $this->name;
    }

    public function __construct()
    {
        $this->kloKiEvents = new ArrayCollection();
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

    public function getNettopreis(): ?int
    {
        return $this->nettopreis;
    }

    public function setNettopreis(int $nettopreis): self
    {
        $this->nettopreis = $nettopreis;

        return $this;
    }

    /**
     * @return Collection|KloKiEvent[]
    public function getKloKiEvents(): Collection
    {
        return $this->kloKiEvents;
    }

    public function addKloKiEvent(KloKiEvent $kloKiEvent): self
    {
        if (!$this->kloKiEvents->contains($kloKiEvent)) {
            $this->kloKiEvents[] = $kloKiEvent;
            $kloKiEvent->addAusstattung($this);
        }

        return $this;
    }

    public function removeKloKiEvent(KloKiEvent $kloKiEvent): self
    {
        if ($this->kloKiEvents->contains($kloKiEvent)) {
            $this->kloKiEvents->removeElement($kloKiEvent);
            $kloKiEvent->removeAusstattung($this);
        }

        return $this;
    }
     */
}
