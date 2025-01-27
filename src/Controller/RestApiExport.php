<?php

namespace App\Controller;

use App\Services\BackgroundTask\BackgroundTaskManager;
use App\Services\BackgroundTaskCommands\ExportBackgroundTask;
use App\Services\Export\CsvExport;
use App\Services\Export\ExportConfigurationBuilder;
use App\Services\Export\ExportEntityTypeConfiguration;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RestApiExport extends AbstractController{

  #[Route(path: '/api/export/entityType/{entityType}', name: 'app_api_export_entity_type')]
  public function getEntityTypeConfiguration(ExportEntityTypeConfiguration $exportEntityTypeConfiguration, string $entityType): Response {
    $config = $exportEntityTypeConfiguration->getEntityTypeConfiguration($entityType);
    return $this->json($config);
  }

  #[Route(path: '/api/export/start', name: 'app_api_export_start', methods: ['POST'])]
  public function getStartExport(Request $request, CsvExport $csvExport, ExportConfigurationBuilder $configurationBuilder): Response {
    $requestArray = $request->toArray();
    $configuration = $configurationBuilder->build($requestArray);

    dd($csvExport->export($configuration));
  }

  #[Route(path: '/api/export/task/start', name: 'app_api_export_task_start', methods: ['POST'])]
  public function generateExport(Request $request, BackgroundTaskManager $backgroundTaskManager): Response {
    $exportRequest = $request->toArray();
    $task = $backgroundTaskManager->createTask(ExportBackgroundTask::class, $exportRequest);

    if(!$task || !$backgroundTaskManager->executeTask($task)) {
      return $this->json(['status' => 'Error']);

    };

    return $this->json(['status' => 'Startet', 'task' => $task->getId()]);
  }

}