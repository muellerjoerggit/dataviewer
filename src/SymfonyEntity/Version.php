<?php

namespace App\SymfonyEntity;

use App\SymfonyRepository\VersionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VersionRepository::class)]
class Version {

  #[ORM\Id]
  #[ORM\Column]
  private ?string $id = NULL;

  #[ORM\Column(length: 100)]
  private ?string $label = NULL;

  #[ORM\OneToOne(targetEntity: self::class)]
  private ?self $predecessor = NULL;

  #[ORM\OneToOne(targetEntity: self::class)]
  private ?self $successor = NULL;

  public function setId(string $id): static {
    $this->id = $id;
    return $this;
  }

  public function getId(): ?string {
    return $this->id;
  }

  public function getLabel(): ?string {
    return $this->label;
  }

  public function setLabel(string $label): static {
    $this->label = $label;

    return $this;
  }

  public function getPredecessor(): ?self {
    return $this->predecessor;
  }

  public function setPredecessor(?self $predecessor): static {
    $this->predecessor = $predecessor;

    return $this;
  }

  public function getSuccessor(): ?self {
    return $this->successor;
  }

  public function setSuccessor(?self $successor): static {
    $this->successor = $successor;

    return $this;
  }

  public function __toString(): string {
    return $this->getLabel();
  }

}
