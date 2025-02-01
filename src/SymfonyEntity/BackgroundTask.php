<?php

namespace App\SymfonyEntity;

use App\SymfonyRepository\BackgroundTaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BackgroundTaskRepository::class)]
class BackgroundTask {

  public const int STATUS_IDLE = 1;
  public const int STATUS_RUNNING = 2;
  public const int STATUS_ERROR = 3;
  public const int STATUS_FINISHED = 4;

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = NULL;

  #[ORM\Column(length: 100)]
  private ?string $name = NULL;

  #[ORM\Column(type: Types::SMALLINT)]
  private ?int $status = NULL;

  #[ORM\Column(length: 255, nullable: TRUE)]
  private ?string $description = NULL;

  #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: TRUE)]
  private ?\DateTimeInterface $startDate = NULL;

  #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: TRUE)]
  private ?\DateTimeInterface $endDate = NULL;

  #[ORM\OneToOne(mappedBy: 'task', cascade: ['persist', 'remove'])]
  private ?TaskConfiguration $taskConfiguration = NULL;

  #[ORM\Column(type: Types::TEXT, nullable: TRUE)]
  private ?string $progress = NULL;

  #[ORM\Column(type: Types::BOOLEAN, nullable: FALSE, options: ["default" => FALSE])]
  private ?bool $terminate = NULL;

  /**
   * @var Collection<int, TaskResult>
   */
  #[ORM\OneToMany(targetEntity: TaskResult::class, mappedBy: 'task')]
  private Collection $taskResults;

  public function __construct() {
    $this->taskResults = new ArrayCollection();
  }

  public function getId(): ?int {
    return $this->id;
  }

  public function getName(): ?string {
    return $this->name;
  }

  public function setName(string $name): static {
    $this->name = $name;

    return $this;
  }

  public function getStatus(): ?int {
    return $this->status;
  }

  public function setStatus(int $status): static {
    $this->status = $status;

    return $this;
  }

  public function getDescription(): ?string {
    return $this->description;
  }

  public function setDescription(?string $description): static {
    $this->description = $description;

    return $this;
  }

  public function getStartDate(): ?\DateTimeInterface {
    return $this->startDate;
  }

  public function setStartDate(?\DateTimeInterface $startDate): static {
    $this->startDate = $startDate;

    return $this;
  }

  public function getEndDate(): ?\DateTimeInterface {
    return $this->endDate;
  }

  public function setEndDate(?\DateTimeInterface $endDate): static {
    $this->endDate = $endDate;

    return $this;
  }

  public function getTaskConfiguration(): ?TaskConfiguration {
    return $this->taskConfiguration;
  }

  public function setTaskConfiguration(TaskConfiguration $taskConfiguration): static {
    // set the owning side of the relation if necessary
    if ($taskConfiguration->getTask() !== $this) {
      $taskConfiguration->setTask($this);
    }

    $this->taskConfiguration = $taskConfiguration;

    return $this;
  }

  public function getProgress(): ?string {
    return $this->progress;
  }

  public function setProgress(?string $progress): static {
    $this->progress = $progress;

    return $this;
  }

  public function getTerminate(): ?bool {
    return $this->terminate ?? FALSE;
  }

  public function setTerminate(?bool $terminate): static {
    $this->terminate = $terminate;

    return $this;
  }

  /**
   * @return Collection<int, TaskResult>
   */
  public function getTaskResults(): Collection {
    return $this->taskResults;
  }

  public function addTaskResult(TaskResult $taskResult): static {
    if (!$this->taskResults->contains($taskResult)) {
      $this->taskResults->add($taskResult);
      $taskResult->setTask($this);
    }

    return $this;
  }

}
