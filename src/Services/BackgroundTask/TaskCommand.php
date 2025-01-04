<?php

namespace App\Services\BackgroundTask;

use App\SymfonyEntity\BackgroundTask;
use App\SymfonyEntity\TaskConfiguration;
use App\SymfonyEntity\TaskResult;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class TaskCommand extends Command implements BackgroundTaskInterface {

	protected BackgroundTask $task;

	public function __construct(
    protected readonly EntityManagerInterface $entityManager
  ) {
		parent::__construct(null);
	}

	public function getTask(): BackgroundTask {
		return $this->task;
	}

	protected function execute(InputInterface $input, OutputInterface $output): int {
		return Command::FAILURE;
	}

	protected function loadTask(int $taskID): BackgroundTask | null {
		$task = $this->entityManager->getRepository(BackgroundTask::class)->find($taskID);
		if(!$task) {
			return null;
		}

		$this->task = $task;
		return $task;
	}

	protected function createResult(mixed $resultData): string | null {
		return null;
	}

	protected function persistResult(string $resultJson, int $resultType, BackgroundTask $task): void {
		$taskResult = new TaskResult();
		$taskResult
			->setTask($task)
			->setType($resultType)
			->setResult($resultJson);

		$this->entityManager->persist($taskResult);
		$this->entityManager->flush();
	}

	protected function finishTask(BackgroundTask $task): void {
		$task->setStatus(BackgroundTask::STATUS_FINISHED);
		$task->setEnd(new \DateTime());

		$this->entityManager->persist($task);
		$this->entityManager->flush();
	}

	public static function buildTaskConfiguration(TaskConfiguration $taskConfiguration, mixed $configuration): bool {
		$configuration = json_encode($configuration);

		if(!$configuration) {
			return false;
		}

		$taskConfiguration
			->setCommand('export')
			->setConfiguration($configuration);

		return true;
	}

	protected function abortTask(BackgroundTask $task): void {
		$task
			->setStatus(BackgroundTask::STATUS_ERROR)
			->setEnd(new \DateTime());
		$this->entityManager->persist($task);
		$this->entityManager->flush();
	}

	protected function startTask(BackgroundTask $task): void {
		$task
			->setStatus(BackgroundTask::STATUS_RUNNING)
			->setStart(new \DateTime());
		$this->entityManager->persist($task);
		$this->entityManager->flush();
	}

	protected function getTaskConfiguration(BackgroundTask $task): array {
		$configuration = $task->getTaskConfiguration()->getConfiguration();
		return json_decode($configuration, true) ?? [];
	}

}
