<?php

namespace App\Services\Export\PathExporter;

use App\Services\AbstractLocator;
use App\Services\Export\ExportData\PathExport;
use App\Services\Export\PathExport_Handler\CommonPathExportHandler;
use App\Services\Export\PathExport_Handler\NullPathExportHandler;
use Generator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class PathExporterLocator extends AbstractLocator {

  public function __construct(
    #[AutowireLocator('exporter.path_handler')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getPathExportHandler(PathExport $exportPath): PathExportHandlerInterface {
    $handler = $exportPath->getPathExporterClass();

    if ($handler && $this->has($handler)) {
      return $this->get($handler);
    } else {
      return $this->get(NullPathExportHandler::class);
    }
  }

  /**
   * @return Generator<PathExportHandlerInterface>
   */
  public function iteratePathExporters(): Generator {
    foreach ($this->getProvidedServices() as $service) {
      yield $this->get($service);
    }
  }

}
