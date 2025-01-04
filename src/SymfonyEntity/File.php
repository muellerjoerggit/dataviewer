<?php

namespace App\SymfonyEntity;

use App\SymfonyRepository\FileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FileRepository::class)]
class File {

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = NULL;

  #[ORM\Column(length: 255)]
  private ?string $server_filename = NULL;

  #[ORM\Column(length: 255)]
  private ?string $filename = NULL;

  #[ORM\Column]
  private ?int $type = NULL;

  #[ORM\Column(length: 255)]
  private ?string $mimetype = NULL;

  public function getId(): ?int {
    return $this->id;
  }

  public function getServerFilename(): ?string {
    return $this->server_filename;
  }

  public function setServerFilename(string $server_filename): static {
    $this->server_filename = $server_filename;

    return $this;
  }

  public function getFilename(): ?string {
    return $this->filename;
  }

  public function setFilename(string $filename): static {
    $this->filename = $filename;

    return $this;
  }

  public function getType(): ?int {
    return $this->type;
  }

  public function setType(int $type): static {
    $this->type = $type;

    return $this;
  }

  public function getMimetype(): ?string {
    return $this->mimetype;
  }

  public function setMimetype(string $mimetype): static {
    $this->mimetype = $mimetype;

    return $this;
  }

}
