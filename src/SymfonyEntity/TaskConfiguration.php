<?php

namespace App\SymfonyEntity;

use App\SymfonyRepository\TaskConfigurationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskConfigurationRepository::class)]
class TaskConfiguration {

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = NULL;

  #[ORM\OneToOne(inversedBy: 'taskConfiguration', cascade: [
    'persist',
    'remove',
  ])]
  #[ORM\JoinColumn(nullable: FALSE)]
  private ?BackgroundTask $task = NULL;

  #[ORM\Column(length: 100)]
  private ?string $command = NULL;

  #[ORM\Column(type: Types::TEXT, nullable: TRUE)]
  private ?string $configuration = NULL;

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

  public function getCommand(): ?string {
    return $this->command;
  }

  public function setCommand(string $command): static {
    $this->command = $command;

    return $this;
  }

  public function getConfiguration(): ?string {
    return $this->configuration;
  }

  public function setConfiguration(?string $configuration): static {
    $this->configuration = $configuration;

    return $this;
  }

}
