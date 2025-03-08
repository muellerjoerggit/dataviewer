<?php

namespace App\Development\Controller;

use App\Services\DirectoryFileService;
use App\Services\Export\CsvExport;
use App\Services\Export\ExportConfigurationBuilder;
use App\SymfonyRepository\BackgroundTaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;

class Export extends AbstractController {

  public function export(CsvExport $csvExport, BackgroundTaskRepository $taskRepository, ExportConfigurationBuilder $configurationBuilder, DirectoryFileService $fileRegister): Response {
    $task = $taskRepository->find(10);
    $configuration = $task->getTaskConfiguration()->getConfiguration();
    $configuration = json_decode($configuration, true);
    $exportData = $configurationBuilder->build($configuration);

    $csv = $csvExport->export($exportData);

    $tempDir = $fileRegister->getTempDir();
    $tmpFileName = (new Filesystem())->tempnam($tempDir, 'sb_', '.csv');
    $tmpFile = fopen($tmpFileName, 'wb+');
    if (!\is_resource($tmpFile)) {
      throw new \RuntimeException('Unable to create a temporary file.');
    }

    $csv = iconv("UTF-8", "Windows-1252//IGNORE", $csv);

    file_put_contents($tmpFileName, $csv);

    fclose($tmpFile);

    return $this->file($tmpFileName);
  }

}