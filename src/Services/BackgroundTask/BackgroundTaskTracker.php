<?php

namespace App\Services\BackgroundTask;

use App\Services\ProgressTracker\TrackerInterface;
use App\SymfonyEntity\BackgroundTask;
use App\SymfonyRepository\BackgroundTaskRepository;
use Doctrine\ORM\EntityManagerInterface;

class BackgroundTaskTracker implements TrackerInterface {

  public function __construct(
    private readonly EntityManagerInterface $entityManager,
    private readonly BackgroundTaskRepository $backgroundTaskRepository,
    private readonly BackgroundTask $backgroundTask,
  ) {}

  public function setProgress(mixed $progress): void {
    if(!$this->validateProgress($progress)) {
      return;
    }

    $this->backgroundTask->setProgress($progress);

    $this->entityManager->persist($this->backgroundTask);
    $this->entityManager->flush();
  }

  public function isTerminated(): bool {
    $id = $this->backgroundTask->getId();
    $task = $this->backgroundTaskRepository->find($id);

    if(!$task) {
      return false;
    }

    return $task->getTerminate();
  }

  private function validateProgress(mixed $progress): bool {
    if(!is_string($progress)) {
      return false;
    }
    return true;
  }
}