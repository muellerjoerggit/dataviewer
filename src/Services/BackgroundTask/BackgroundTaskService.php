<?php

namespace App\Services\BackgroundTask;

use App\Services\DateTimeConverter;
use App\SymfonyEntity\BackgroundTask;
use App\SymfonyRepository\BackgroundTaskRepository;
use Doctrine\ORM\EntityManagerInterface;

class BackgroundTaskService {

  public function __construct(
    private readonly EntityManagerInterface $entityManager,
    private readonly BackgroundTaskRepository $backgroundTaskRepository,
    private readonly DateTimeConverter $dateTimeConverter
  ) {}

  public function getBackgroundTaskByTaskId(int $taskId): BackgroundTask | null {
    return $this->backgroundTaskRepository->find($taskId);
  }

  public function terminateTask(int $taskId): void {
    $task = $this->getBackgroundTaskByTaskId($taskId);

    if($task) {
      $task->setTerminate(true);
      $this->entityManager->persist($task);
      $this->entityManager->flush();
    }
  }

  public function getBackgroundTaskData(int $taskId): array {
    $task = $this->getBackgroundTaskByTaskId($taskId);

    if(!$task) {
      return [
        'taskId' => 0,
        'name' => 'Unbekannter Task',
        'status' => BackgroundTask::STATUS_ERROR,
        'description' => 'Task nicht gefunden',
        'start' => '',
        'end' => '',
        'progress' => '',
      ];
    }

    return [
      'taskId' => $task->getId(),
      'name' => $task->getName(),
      'status' => $task->getStatus(),
      'description' => $task->getDescription() ?? '',
      'start' => $this->dateTimeConverter->formatDateTime($task->getStartDate()),
      'end' => $this->dateTimeConverter->formatDateTime($task->getEndDate()),
      'progress' => json_decode($task->getProgress(), true)
    ];

  }
}