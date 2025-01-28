<?php

namespace App\SymfonyEntity;

use App\SymfonyRepository\TaskResultRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskResultRepository::class)]
class TaskResult {

  public const int TYPE_URL = 1;
  public const int TYPE_FILE = 2;
  public const int TYPE_ENTITY_LIST = 3;

  public const array VALID_TYPES = [
    self::TYPE_URL,
    self::TYPE_FILE,
    self::TYPE_ENTITY_LIST,
  ];

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = NULL;

  #[ORM\ManyToOne(inversedBy: 'taskResult')]
  #[ORM\JoinColumn(nullable: FALSE)]
  private ?BackgroundTask $task = NULL;

  #[ORM\Column(type: Types::TEXT, nullable: TRUE)]
  private ?string $result = NULL;

  #[ORM\Column]
  private ?int $type = NULL;
  public function getId(): ?int {
    return $this->id;
  }

  public function getTask(): ?BackgroundTask {
    return $this->task;
  }

  public function setTask(BackgroundTask $task): static {
    $this->task = $task;

    return $this;
  }

  public function getResult(): ?string {
    return $this->result;
  }

  public function setResult(?string $result): static {
    $this->result = $result;

    return $this;
  }

  public function getType(): ?int {
    return $this->type;
  }

  public function setType(int $type): static {
    $this->type = $type;

    return $this;
  }

}
