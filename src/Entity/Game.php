<?php

namespace App\Entity;

use App\Entity\Event;
use App\Entity\GiftQuantity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\GameRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GameRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotNull(message:"Le champs ne doit pas etre nul")]
    #[Assert\Type(type:"integer")]
    #[ORM\Column]
    private ?int $number = null;

    #[Assert\NotBlank(message:"Le champs ne doit pas etre nul")]
    #[ORM\Column(length: 100)]      
    private ?string $cityName = null;

    #[Assert\NotNull(message:"Le champs ne doit pas etre nul")]
    #[Assert\Type(type:"integer")]
    #[ORM\Column]
    private ?int $cityCode = null;

    #[Assert\Type(type:"DateTime")]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[Assert\Valid]
    #[ORM\OneToMany(mappedBy: 'game', targetEntity: GiftQuantity::class, cascade:["persist"])]
    private Collection $gifts;

    #[Assert\Valid]
    #[ORM\OneToMany(mappedBy: 'game', targetEntity: Event::class, cascade:["persist"])]
    private Collection $events;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ["persist"])]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]
    private ?User $user = null;

    #[ORM\OneToMany(targetEntity: Participant::class, mappedBy: 'game', cascade:["persist"])]
    private Collection $participant;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Participant $tombolaWinner = null;

    #[ORM\Column]
    private ?int $type = null;

    public function __construct()
    {
        $this->gifts = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->participant = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getCityName(): ?string
    {
        return $this->cityName;
    }

    public function setCityName(string $cityName): static
    {
        $this->cityName = $cityName;

        return $this;
    }

    public function getCityCode(): ?int
    {
        return $this->cityCode;
    }

    public function setCityCode(int $cityCode): static
    {
        $this->cityCode = $cityCode;

        return $this;
    }


    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

 

    /**
     * @return Collection<int, GiftQuantity>
     */
    public function getGifts(): Collection
    {
        return $this->gifts;
    }

    public function addGift(GiftQuantity $gift): static
    {
        if (!$this->gifts->contains($gift)) {
            $this->gifts->add($gift);
            $gift->setGame($this);
        }

        return $this;
    }

    public function removeGift(GiftQuantity $gift): static
    {
        if ($this->gifts->removeElement($gift)) {
            // set the owning side to null (unless already changed)
            if ($gift->getGame() === $this) {
                $gift->setGame(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setGame($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getGame() === $this) {
                $event->setGame(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getParticipant(): Collection
    {
        return $this->participant;
    }

    public function addParticipant(Participant $participant): static
    {
        if (!$this->participant->contains($participant)) {
            $this->participant->add($participant);
            $participant->setGame($this);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): static
    {
        if ($this->participant->removeElement($participant)) {
            // set the owning side to null (unless already changed)
            if ($participant->getGame() === $this) {
                $participant->setGame(null);
            }
        }

        return $this;
    }

    public function getTombolaWinner(): ?Participant
    {
        return $this->tombolaWinner;
    }

    public function setTombolaWinner(?Participant $tombolaWinner): static
    {
        $this->tombolaWinner = $tombolaWinner;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): static
    {
        $this->type = $type;

        return $this;
    }
}
