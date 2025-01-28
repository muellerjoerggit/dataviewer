<?php

namespace App\Services\BackgroundTaskCommands;

use App\Services\BackgroundTask\BackgroundTaskInterface;
use App\Services\ProgressTracker\TrackerInterface;
use App\SymfonyEntity\BackgroundTask;
use App\SymfonyEntity\File;
use App\SymfonyEntity\TaskConfiguration;
use App\SymfonyEntity\TaskResult;
use App\SymfonyRepository\BackgroundTaskRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;

abstract class AbstractBackgroundTask extends Command implements BackgroundTaskInterface {

  public function __construct(
    protected readonly BackgroundTaskRepository $backgroundTaskRepository,
    protected readonly EntityManagerInterface $entityManager,
    protected readonly LoggerInterface $logger,
  ) {
    parent::__construct();
  }

  protected function getTask(int $taskID): BackgroundTask | null {
    $task = $this->backgroundTaskRepository->find($taskID);
    if(!$task) {
      return null;
    }
    return $task;
  }

  protected function createResult(mixed $resultData, BackgroundTask $task): bool {
    if($resultData instanceof File) {
      $type = TaskResult::TYPE_FILE;
      $result = json_encode(['fileId' => $resultData->getId()]);
    } else {
      return false;
    }

    $task->setStatus(BackgroundTask::STATUS_FINISHED);
    $task->setEndDate(new DateTime());

    $taskResult = new TaskResult();
    $taskResult
      ->setTask($task)
      ->setType($type)
      ->setResult($result);

    $this->entityManager->persist($taskResult);
    $this->entityManager->persist($task);
    $this->entityManager->flush();

    return true;
  }

  protected function startTask(BackgroundTask $task): void {
    $task
      ->setStatus(BackgroundTask::STATUS_RUNNING)
      ->setStartDate(new DateTime());

    $this->entityManager->persist($task);
    $this->entityManager->flush();
  }

  protected function setTaskFailed(BackgroundTask $task): void {
    $task
      ->setStatus(BackgroundTask::STATUS_ERROR)
      ->setEndDate(new DateTime());

    $this->entityManager->persist($task);
    $this->entityManager->flush();
  }

  protected static function buildTaskConfigurationInternal(TaskConfiguration $taskConfiguration, string $command, mixed $configuration): bool {
    $configuration = json_encode($configuration);

    if(!$configuration) {
      return false;
    }

    $taskConfiguration
      ->setCommand($command)
      ->setConfiguration($configuration);

    return true;
  }

  protected function buildProgressTracker(string $progressClass, BackgroundTask $task): TrackerInterface | null {
    if(!$this->validProgressTracker($progressClass)) {
      return null;
    }

    return new $progressClass(
      $this->entityManager,
      $this->backgroundTaskRepository,
      $task,
    );
  }

  protected function validProgressTracker(string $progressClass): bool {
    try {
      $reflection = new ReflectionClass($progressClass);
    } catch (ReflectionException $exception) {
      return false;
    }

    return array_key_exists(TrackerInterface::class, $reflection->getInterfaces());
  }

}