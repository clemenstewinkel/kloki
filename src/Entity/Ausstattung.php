<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AusstattungRepository")
 */
class Ausstattung
{
    private $mwStSatz = 19;

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
    private $bruttopreis;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\KloKiEvent", mappedBy="Ausstattung")
     */
    private $kloKiEvents;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }


    public function getBruttoPreis(): ?int
    {
        return $this->bruttopreis;
    }

    public function setBruttopreis(int $bruttopreis): self
    {
        $this->bruttopreis = $bruttopreis;

        return $this;
    }

    /**
     * @return Collection|KloKiEvent[]
     */
    public function getKloKiEvents(): Collection
    {
        return $this->kloKiEvents;
    }

    public function getNettopreis(): ?int
    {
        return $this->bruttopreis - $this->getMwSt();
    }

    public function getMwSt(): ?int
    {
        return round($this->bruttopreis / (100 + $this->mwStSatz ) * ($this->mwStSatz));
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

}
