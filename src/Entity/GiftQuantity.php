<?php

namespace App\Entity;

use App\Repository\GiftQuantityRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GiftQuantityRepository::class)]
class GiftQuantity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Type(type:"integer")]
    #[ORM\Column]
    private ?int $initialQuantity = null;

    #[Assert\Type(type:"integer")]
    #[ORM\Column]
    private ?int $quantityUsed = null;

    #[ORM\ManyToOne(inversedBy: 'gifts')]
    private ?Game $game = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInitialQuantity(): ?int
    {
        return $this->initialQuantity;
    }

    public function setInitialQuantity(int $initialQuantity): static
    {
        $this->initialQuantity = $initialQuantity;

        return $this;
    }

    public function getQuantityUsed(): ?int
    {
        return $this->quantityUsed;
    }

    public function setQuantityUsed(int $quantityUsed): static
    {
        $this->quantityUsed = $quantityUsed;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }
}
