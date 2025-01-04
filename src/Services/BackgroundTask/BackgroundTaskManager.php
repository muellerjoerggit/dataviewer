<?php

namespace App\Services\BackgroundTask;

use App\Services\AppNamespaces;
use App\Services\DirectoryFileRegister;
use App\SymfonyEntity\BackgroundTask;
use App\SymfonyEntity\TaskConfiguration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\PhpExecutableFinder;

class BackgroundTaskManager {

	public const int PROGRESS_TYPE_COUNT_ENTITIES = 1;
	public const int MAX_EXECUTION_TIME = 10800;

	private array $commands = [];

	public function __construct(
    private readonly EntityManagerInterface $entityManager,
    DirectoryFileRegister $directoryFileRegister
  ) {
		$dir = $directoryFileRegister->getTaskCommandDir();
		$this->getAllValidCommands($dir);
	}

	private function getAllValidCommands(string $dir): void {
		try {
			$finder = new Finder();
			$finder->files()->in($dir);
		} catch (DirectoryNotFoundException $exception) {
			return;
		}

		if(!$finder->hasResults()) {
			return;
		}

		foreach ($finder->files()->name(['*.php'])->getIterator() as $file) {
			if(preg_match('/^([a-zA-Z]+).php/i', $file->getFilename(), $matches)) {
				$shortName = $matches[1];
				$className = AppNamespaces::buildNamespace(AppNamespaces::NAMESPACE_EXPORT_TASK, $shortName);

				if(!$this->validateTaskClass($className)) {
					continue;
				}

        $command = call_user_func([$className, 'getDefaultName']);

        if(!$command) {
          continue;
        }

				$this->commands[] = $command;
			}
		}

	}

	public function createTask(string $className, mixed $configuration): BackgroundTask | bool {
		$task = new BackgroundTask();
		$taskConfiguration = $this->createTaskConfiguration($className, $configuration);

		if(!$taskConfiguration) {
			return false;
		}

		$task
			->setName('')
			->setTaskConfiguration($taskConfiguration)
			->setStatus(BackgroundTask::STATUS_IDLE)
			->setTerminate(false);

		$this->entityManager->persist($task);
		$this->entityManager->flush();

		return $task;
	}

	private function createTaskConfiguration(string $className, mixed $configuration): TaskConfiguration | bool {
		$taskConfiguration = new TaskConfiguration();

		if(!$this->validateTaskClass($className)) {
			return false;
		}

		$result = call_user_func([$className, 'buildTaskConfiguration'], $taskConfiguration, $configuration);

		if(!$result) {
			return false;
		}

		return $taskConfiguration;
	}

	private function validateTaskClass(string $className): bool {
		try {
			$reflection = new \ReflectionClass($className);
		} catch (\ReflectionException $e) {
			return false;
		}

		if(array_key_exists (BackgroundTaskInterface::class, $reflection->getInterfaces())) {
			return true;
		}

		return false;
	}

	public function executeTask(BackgroundTask $task): void {
		$taskConfiguration = $task->getTaskConfiguration();
		$command = 'task:' . $taskConfiguration->getCommand();

		if(!in_array($command, $this->commands)) {
			return;
		}

		$phpBinaryPath = (new PhpExecutableFinder())->find();

		shell_exec($phpBinaryPath . ' -d max_execution_time=' . self::MAX_EXECUTION_TIME . ' /var/www/html/bin/console ' . $command . ' ' . $task->getId() . ' 2>/dev/null >/dev/null &');
	}
}
