<?php

namespace App\Services\Export\GroupExporter;

use App\Services\AbstractLocator;
use App\Services\Export\ExportData\ExportGroup;
use App\Services\Export\GroupExporter_Handler\NullGroupExporterHandler;
use Generator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class GroupExporterLocator extends AbstractLocator {

  public function __construct(
    #[AutowireLocator('exporter.group_handler')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getGroupExporter(ExportGroup $exportGroup): GroupExporterHandlerInterface {
    $handler = $exportGroup->getExporterClass();

    if ($handler && $this->has($handler)) {
      return $this->get($handler);
    } else {
      return $this->get(NullGroupExporterHandler::class);
    }
  }

  /**
   * @return Generator<GroupExporterHandlerInterface>
   */
  public function iterateGroupExporters(): Generator {
    foreach ($this->getProvidedServices() as $service) {
      yield $this->get($service);
    }
  }

}