<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *     fields={"email"},
 *     message="Diese Email-Adresse existiert schon!"
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"events:read"})*
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="Bitte eine E-Mail-Adresse eingeben.")
     * @Assert\Email(message="Das ist keine gültige E-Mail-Adresse!")
     * @Groups({"events:read"})*
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Groups({"events:read"})*
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @Assert\NotBlank(message="Bitte ein Passwort eingeben!")
     * @Assert\Length(min=5, minMessage="Das Passwort muss mindestens 5 Zeichen lang sein!")
     */
    private $plainPassword;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\KloKiEvent", mappedBy="availableHelpers")
     */
    private $kloKiEvents;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\KloKiEvent", mappedBy="helperEinlassEins")
     */
    private $einlassEinsAtEvents;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\KloKiEvent", mappedBy="helperEinlassZwei")
     */
    private $einlassZweiAtEvents;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\KloKiEvent", mappedBy="helperKasse")
     */
    private $kasseAtEvents;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\KloKiEvent", mappedBy="helperSpringerEins")
     */
    private $springerEinsAtEvents;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\KloKiEvent", mappedBy="helperSpringerZwei")
     */
    private $springerZweiAtEvents;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\KloKiEvent", mappedBy="LichtTechniker")
     */
    private $eventsLicht;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\KloKiEvent", mappedBy="TonTechniker")
     */
    private $eventsTon;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Addresse", cascade={"persist", "remove"})
     */
    private $address;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\KloKiEvent", mappedBy="helperGarderobe")
     */
    private $garderobeAtEvents;

    public function __construct()
    {
        $this->kloKiEvents = new ArrayCollection();
        $this->einlassEinsAtEvents = new ArrayCollection();
        $this->einlassZweiAtEvents = new ArrayCollection();
        $this->kasseAtEvents = new ArrayCollection();
        $this->springerEinsAtEvents = new ArrayCollection();
        $this->springerZweiAtEvents = new ArrayCollection();
        $this->eventsLicht = new ArrayCollection();
        $this->eventsTon = new ArrayCollection();
        $this->garderobeAtEvents = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function __toString() : string
    {
        $answer = $this->email;
        if($this->name) $answer .= ' (' . $this->name . ')';
        return $answer;
    }

    /**
     * @param mixed $plainPassword
     */
    public function setPlainPassword($plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
            $kloKiEvent->addAvailableHelper($this);
        }

        return $this;
    }

    public function removeKloKiEvent(KloKiEvent $kloKiEvent): self
    {
        if ($this->kloKiEvents->contains($kloKiEvent)) {
            $this->kloKiEvents->removeElement($kloKiEvent);
            $kloKiEvent->removeAvailableHelper($this);
        }

        return $this;
    }

    /**
     * @return Collection|KloKiEvent[]
     */
    public function getEinlassEinsAtEvents(): Collection
    {
        return $this->einlassEinsAtEvents;
    }

    public function addEinlassEinsAtEvent(KloKiEvent $einlassEinsAtEvent): self
    {
        if (!$this->einlassEinsAtEvents->contains($einlassEinsAtEvent)) {
            $this->einlassEinsAtEvents[] = $einlassEinsAtEvent;
            $einlassEinsAtEvent->setHelperEinlassEins($this);
        }

        return $this;
    }

    public function removeEinlassEinsAtEvent(KloKiEvent $einlassEinsAtEvent): self
    {
        if ($this->einlassEinsAtEvents->contains($einlassEinsAtEvent)) {
            $this->einlassEinsAtEvents->removeElement($einlassEinsAtEvent);
            // set the owning side to null (unless already changed)
            if ($einlassEinsAtEvent->getHelperEinlassEins() === $this) {
                $einlassEinsAtEvent->setHelperEinlassEins(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|KloKiEvent[]
     */
    public function getEinlassZweiAtEvents(): Collection
    {
        return $this->einlassZweiAtEvents;
    }

    public function addEinlassZweiAtEvent(KloKiEvent $einlassZweiAtEvent): self
    {
        if (!$this->einlassZweiAtEvents->contains($einlassZweiAtEvent)) {
            $this->einlassZweiAtEvents[] = $einlassZweiAtEvent;
            $einlassZweiAtEvent->setHelperEinlassZwei($this);
        }

        return $this;
    }

    public function removeEinlassZweiAtEvent(KloKiEvent $einlassZweiAtEvent): self
    {
        if ($this->einlassZweiAtEvents->contains($einlassZweiAtEvent)) {
            $this->einlassZweiAtEvents->removeElement($einlassZweiAtEvent);
            // set the owning side to null (unless already changed)
            if ($einlassZweiAtEvent->getHelperEinlassZwei() === $this) {
                $einlassZweiAtEvent->setHelperEinlassZwei(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|KloKiEvent[]
     */
    public function getKasseAtEvents(): Collection
    {
        return $this->kasseAtEvents;
    }

    public function addKasseAtEvent(KloKiEvent $kasseAtEvent): self
    {
        if (!$this->kasseAtEvents->contains($kasseAtEvent)) {
            $this->kasseAtEvents[] = $kasseAtEvent;
            $kasseAtEvent->setHelperKasse($this);
        }

        return $this;
    }

    public function removeKasseAtEvent(KloKiEvent $kasseAtEvent): self
    {
        if ($this->kasseAtEvents->contains($kasseAtEvent)) {
            $this->kasseAtEvents->removeElement($kasseAtEvent);
            // set the owning side to null (unless already changed)
            if ($kasseAtEvent->getHelperKasse() === $this) {
                $kasseAtEvent->setHelperKasse(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|KloKiEvent[]
     */
    public function getSpringerEinsAtEvents(): Collection
    {
        return $this->springerEinsAtEvents;
    }

    public function addSpringerEinsAtEvent(KloKiEvent $springerEinsAtEvent): self
    {
        if (!$this->springerEinsAtEvents->contains($springerEinsAtEvent)) {
            $this->springerEinsAtEvents[] = $springerEinsAtEvent;
            $springerEinsAtEvent->setHelperSpringerEins($this);
        }

        return $this;
    }

    public function removeSpringerEinsAtEvent(KloKiEvent $springerEinsAtEvent): self
    {
        if ($this->springerEinsAtEvents->contains($springerEinsAtEvent)) {
            $this->springerEinsAtEvents->removeElement($springerEinsAtEvent);
            // set the owning side to null (unless already changed)
            if ($springerEinsAtEvent->getHelperSpringerEins() === $this) {
                $springerEinsAtEvent->setHelperSpringerEins(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|KloKiEvent[]
     */
    public function getSpringerZweiAtEvents(): Collection
    {
        return $this->springerZweiAtEvents;
    }

    public function addSpringerZweiAtEvent(KloKiEvent $springerZweiAtEvent): self
    {
        if (!$this->springerZweiAtEvents->contains($springerZweiAtEvent)) {
            $this->springerZweiAtEvents[] = $springerZweiAtEvent;
            $springerZweiAtEvent->setHelperSpringerZwei($this);
        }

        return $this;
    }

    public function removeSpringerZweiAtEvent(KloKiEvent $springerZweiAtEvent): self
    {
        if ($this->springerZweiAtEvents->contains($springerZweiAtEvent)) {
            $this->springerZweiAtEvents->removeElement($springerZweiAtEvent);
            // set the owning side to null (unless already changed)
            if ($springerZweiAtEvent->getHelperSpringerZwei() === $this) {
                $springerZweiAtEvent->setHelperSpringerZwei(null);
            }
        }

        return $this;
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

    /**
     * @return Collection|KloKiEvent[]
     */
    public function getEventsLicht(): Collection
    {
        return $this->eventsLicht;
    }

    public function addEventsLicht(KloKiEvent $eventsLicht): self
    {
        if (!$this->eventsLicht->contains($eventsLicht)) {
            $this->eventsLicht[] = $eventsLicht;
            $eventsLicht->setLichtTechniker($this);
        }

        return $this;
    }

    public function removeEventsLicht(KloKiEvent $eventsLicht): self
    {
        if ($this->eventsLicht->contains($eventsLicht)) {
            $this->eventsLicht->removeElement($eventsLicht);
            // set the owning side to null (unless already changed)
            if ($eventsLicht->getLichtTechniker() === $this) {
                $eventsLicht->setLichtTechniker(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|KloKiEvent[]
     */
    public function getEventsTon(): Collection
    {
        return $this->eventsTon;
    }

    public function addEventsTon(KloKiEvent $eventsTon): self
    {
        if (!$this->eventsTon->contains($eventsTon)) {
            $this->eventsTon[] = $eventsTon;
            $eventsTon->setTonTechniker($this);
        }

        return $this;
    }

    public function removeEventsTon(KloKiEvent $eventsTon): self
    {
        if ($this->eventsTon->contains($eventsTon)) {
            $this->eventsTon->removeElement($eventsTon);
            // set the owning side to null (unless already changed)
            if ($eventsTon->getTonTechniker() === $this) {
                $eventsTon->setTonTechniker(null);
            }
        }

        return $this;
    }

    public function getAddress(): ?Addresse
    {
        return $this->address;
    }

    public function setAddress(?Addresse $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function isDeletable(): bool
    {
        return (
            $this->getEinlassEinsAtEvents()->isEmpty() &&
            $this->getEinlassZweiAtEvents()->isEmpty() &&
            $this->getKasseAtEvents()->isEmpty() &&
            $this->getSpringerEinsAtEvents()->isEmpty() &&
            $this->getSpringerZweiAtEvents()->isEmpty() &&
            $this->getEventsLicht()->isEmpty() &&
            $this->getEventsTon()->isEmpty()
        );
    }

    /**
     * @return Collection|KloKiEvent[]
     */
    public function getGarderobeAtEvents(): Collection
    {
        return $this->garderobeAtEvents;
    }

    public function addGarderobeAtEvent(KloKiEvent $garderobeAtEvent): self
    {
        if (!$this->garderobeAtEvents->contains($garderobeAtEvent)) {
            $this->garderobeAtEvents[] = $garderobeAtEvent;
            $garderobeAtEvent->setHelperGarderobe($this);
        }

        return $this;
    }

    public function removeGarderobeAtEvent(KloKiEvent $garderobeAtEvent): self
    {
        if ($this->garderobeAtEvents->contains($garderobeAtEvent)) {
            $this->garderobeAtEvents->removeElement($garderobeAtEvent);
            // set the owning side to null (unless already changed)
            if ($garderobeAtEvent->getHelperGarderobe() === $this) {
                $garderobeAtEvent->setHelperGarderobe(null);
            }
        }

        return $this;
    }
}
