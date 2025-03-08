<?php

namespace App\Services\BackgroundTask;

use App\Services\AppNamespaces;
use App\Services\DirectoryFileService;
use App\SymfonyEntity\BackgroundTask;
use App\SymfonyEntity\TaskConfiguration;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\PhpExecutableFinder;

class BackgroundTaskManager {

	public const int PROGRESS_TYPE_COUNT_ENTITIES = 1;
	public const int MAX_EXECUTION_TIME = 10800;

	private array $commands = [];

	public function __construct(
    private readonly EntityManagerInterface $entityManager,
    private readonly LoggerInterface $logger,
    private readonly DirectoryFileService $directoryFileRegister,
  ) {
		$this->getAllValidCommands($directoryFileRegister->getTaskCommandDir());
    $this->logger->debug('Background task manager started');
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
          $this->logger->debug("Command invalid class '$className'");
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

    if(!$this->validateTaskClass($className)) {
      return false;
    }

		$taskConfiguration = $this->createTaskConfiguration($className, $configuration);

		if(!$taskConfiguration) {
			return false;
		}

    $name = call_user_func([$className, 'getTaskName'], $configuration);
    $description = call_user_func([$className, 'getTaskDescription'], $configuration);

		$task
			->setName($name)
      ->setDescription($description)
			->setTaskConfiguration($taskConfiguration)
			->setStatus(BackgroundTask::STATUS_IDLE)
			->setTerminate(false);

		$this->entityManager->persist($task);
		$this->entityManager->flush();

		return $task;
	}

	private function createTaskConfiguration(string $className, mixed $configuration): TaskConfiguration | null {
		$taskConfiguration = new TaskConfiguration();

		$result = call_user_func([$className, 'buildTaskConfiguration'], $taskConfiguration, $configuration);

		if(!$result) {
			return null;
		}

		return $taskConfiguration;
	}

	private function validateTaskClass(string $className): bool {
		try {
			$reflection = new ReflectionClass($className);
		} catch (ReflectionException $e) {
			return false;
		}

		if(array_key_exists(BackgroundTaskInterface::class, $reflection->getInterfaces())) {
			return true;
		}

		return false;
	}

	public function executeTask(BackgroundTask $task): bool {
		$taskConfiguration = $task->getTaskConfiguration();
		$command = 'task:' . $taskConfiguration->getCommand();

		if(!in_array($command, $this->commands)) {
      $this->logger->debug("Invalid Command '$command'");
      $this->logger->debug("Available commands: " . implode(', ', $this->commands));
			return false;
		}

		$phpBinaryPath = $this->directoryFileRegister->getPhpExecutable();
    $consoleDir = $this->directoryFileRegister->getConsoleDir();

		$result = shell_exec($phpBinaryPath . ' -d max_execution_time=' . self::MAX_EXECUTION_TIME . ' ' . $consoleDir . ' ' . $command . ' ' . $task->getId() . ' 2>/dev/null >/dev/null &');

    $this->logger->debug("Command '$command' started with output $result");

    return true;
	}
}
