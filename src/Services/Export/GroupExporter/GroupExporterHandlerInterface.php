<?php

namespace App\Services\Export\GroupExporter;

use App\DaViEntity\EntityInterface;
use App\Services\Export\ExportData\ExportGroup;
use App\Services\Export\ExportRow;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('exporter.group_handler')]
interface GroupExporterHandlerInterface {

  public function fillExportGroup(ExportRow $row, ExportGroup $exportGroup, EntityInterface $entity): void;

  public function getName(): string;

  public function getLabel(): string;

  public function getDescription(): string;

  public function getType(): int;

  public function getHeaderColumn(ExportGroup $exportGroup, string $suffix, bool $firstColumn = false): array;

  public function getRowAsArray(ExportRow $row, ExportGroup $exportGroup): array;

}
