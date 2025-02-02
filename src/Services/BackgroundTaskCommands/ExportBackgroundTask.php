<?php

namespace App\Services\BackgroundTaskCommands;

use App\Services\BackgroundTask\BackgroundTaskTracker;
use App\Services\Export\CsvExport;
use App\Services\Export\ExportConfigurationBuilder;
use App\Services\FileService;
use App\SymfonyEntity\File;
use App\SymfonyEntity\TaskConfiguration;
use App\SymfonyRepository\BackgroundTaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Throwable;

#[AsCommand(name: 'task:export')]
class ExportBackgroundTask extends AbstractBackgroundTask {

  public function __construct(
    private readonly CsvExport $csvExport,
    private readonly ExportConfigurationBuilder $configurationBuilder,
    private readonly FileService $fileService,
    BackgroundTaskRepository $backgroundTaskRepository,
    EntityManagerInterface $entityManager,
    LoggerInterface $logger
  ) {
    parent::__construct($backgroundTaskRepository, $entityManager, $logger);
  }

  protected function configure(): void {
    $this
      ->setDescription('Creates an export file.')
      ->setHelp('This command create an export file.')
      ->addArgument('task_id', InputArgument::REQUIRED, 'The id of the task.');
  }

  protected function execute(InputInterface $input, OutputInterface $output): int {
    $taskId = $input->getArgument('task_id');
    $task = $this->getTask($taskId);

    if(!$task) {
      return Command::FAILURE;
    }

    $configuration = $task->getTaskConfiguration()->getConfiguration();
    $configuration = json_decode($configuration, true);
    $configuration = $this->configurationBuilder->build($configuration);

    if(!$configuration->isValid()) {
      return Command::FAILURE;
    }

    $tracker = $this->buildProgressTracker(BackgroundTaskTracker::class, $task);

    $this->startTask($task);

    $result = false;
    $file = null;
    try {
      $csv = $this->csvExport->export($configuration, $tracker);
      $file = $this->fileService->dumpTempFile(
        $configuration->getFileName(),
        FileService::FILE_TYPE_EXPORT,
        FileService::EXTENSION_CSV,
        $csv
      );
      $result = $this->createResult($file, $task);
    } catch (Throwable $exception) {
      $this->setTaskFailed($task);
      $this->logger->error($exception->getMessage());
    }

    if(!$result || !$file instanceof File) {
      return Command::FAILURE;
    }

    return Command::SUCCESS;
  }

  public static function buildTaskConfiguration(TaskConfiguration $taskConfiguration, mixed $configuration): bool {
    return AbstractBackgroundTask::buildTaskConfigurationInternal($taskConfiguration, 'export', $configuration);
  }

  public static function getTaskName(mixed $configuration): string {
    $entity = $configuration[ExportConfigurationBuilder::CONFIG_EXPORT][ExportConfigurationBuilder::START_ENTITY_PATH][ExportConfigurationBuilder::CONFIG_PATH][ExportConfigurationBuilder::CONFIG_TARGET_ENTITY] ?? '';
    return 'Export ' . $entity;
  }

  public static function getTaskDescription(mixed $configuration): string {
    $description = 'Export Entitäten: ';
    foreach ($configuration[ExportConfigurationBuilder::CONFIG_EXPORT] as $exportPath) {
      $description .= $exportPath[ExportConfigurationBuilder::CONFIG_PATH][ExportConfigurationBuilder::CONFIG_TARGET_ENTITY] . ', ';
    }
    return $description;
  }

}