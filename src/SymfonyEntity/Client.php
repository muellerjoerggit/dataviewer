<?php

namespace App\SymfonyEntity;

use App\SymfonyRepository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client {

  #[ORM\Id]
  #[ORM\Column(length: 100)]
  private ?string $client_id = NULL;

  #[ORM\Column(length: 100)]
  private ?string $name = NULL;

  #[ORM\Column(length: 100)]
  private ?string $database_name = NULL;

  #[ORM\Column(length: 255, nullable: TRUE)]
  private ?string $url = NULL;

  #[ORM\OneToOne(cascade: ['persist', 'remove'])]
  private ?Version $version = NULL;

  public function getClientId(): ?string {
    return $this->client_id;
  }

  public function setClientId(string $client_id): static {
    $this->client_id = $client_id;

    return $this;
  }

  public function getName(): ?string {
    return $this->name;
  }

  public function setName(string $name): static {
    $this->name = $name;

    return $this;
  }

  public function getDatabaseName(): ?string {
    return $this->database_name;
  }

  public function setDatabaseName(string $database_name): static {
    $this->database_name = $database_name;

    return $this;
  }

  public function getUrl(): ?string {
    return $this->url;
  }

  public function setUrl(?string $url): static {
    $this->url = $url;

    return $this;
  }

  public function getVersion(): ?Version {
    return $this->version;
  }

  public function setVersion(?Version $version): static {
    $this->version = $version;

    return $this;
  }

}
