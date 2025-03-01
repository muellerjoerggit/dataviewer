<?php

namespace App\Services\Export\PathExporter;

use App\Services\AbstractLocator;
use App\Services\Export\ExportData\PathExport;
use App\Services\Export\PathExporter_Handler\CommonPathExporter;
use App\Services\Export\PathExporter_Handler\NullPathExporter;
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

  public function getPathExporter(PathExport $exportPath): PathExporterHandlerInterface {
//    $handler = $exportPath->getPathExporterClass();
    $handler = CommonPathExporter::class;

    if ($handler && $this->has($handler)) {
      return $this->get($handler);
    } else {
      return $this->get(NullPathExporter::class);
    }
  }

  /**
   * @return Generator<PathExporterHandlerInterface>
   */
  public function iteratePathExporters(): Generator {
    foreach ($this->getProvidedServices() as $service) {
      yield $this->get($service);
    }
  }

}