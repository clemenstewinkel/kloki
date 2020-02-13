<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BestuhlungsplanRepository")
 */
class Bestuhlungsplan
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
    private $sitzplaetze;

    /**
     * @ORM\Column(type="integer")
     */
    private $stehplaetze;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pdfFilePath;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pngFilePath;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\KloKiEvent", mappedBy="bestPlan")
     */
    private $kloKiEvents;

    /**
     * @ORM\Column(type="integer")
     */
    private $sitzplaetzeOben;

    public function __construct()
    {
        $this->kloKiEvents = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name . ' (Sitz: ' . $this->getSitzplaetze() . ', Steh: ' . $this->getStehplaetze() . ')';
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

    public function getSitzplaetze(): ?int
    {
        return $this->sitzplaetze;
    }

    public function setSitzplaetze(int $sitzplaetze): self
    {
        $this->sitzplaetze = $sitzplaetze;

        return $this;
    }

    public function getStehplaetze(): ?int
    {
        return $this->stehplaetze;
    }

    public function setStehplaetze(int $stehplaetze): self
    {
        $this->stehplaetze = $stehplaetze;

        return $this;
    }

    public function getPdfFilePath(): ?string
    {
        return $this->pdfFilePath;
    }

    public function setPdfFilePath(string $pdfFilePath): self
    {
        $this->pdfFilePath = $pdfFilePath;

        return $this;
    }

    public function getPngFilePath(): ?string
    {
        return $this->pngFilePath;
    }

    public function setPngFilePath(string $pngFilePath): self
    {
        $this->pngFilePath = $pngFilePath;

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
            $kloKiEvent->setBestPlan($this);
        }

        return $this;
    }

    public function removeKloKiEvent(KloKiEvent $kloKiEvent): self
    {
        if ($this->kloKiEvents->contains($kloKiEvent)) {
            $this->kloKiEvents->removeElement($kloKiEvent);
            // set the owning side to null (unless already changed)
            if ($kloKiEvent->getBestPlan() === $this) {
                $kloKiEvent->setBestPlan(null);
            }
        }

        return $this;
    }

    public function getSitzplaetzeOben(): ?int
    {
        return $this->sitzplaetzeOben;
    }

    public function setSitzplaetzeOben(int $sitzplaetzeOben): self
    {
        $this->sitzplaetzeOben = $sitzplaetzeOben;

        return $this;
    }
}
