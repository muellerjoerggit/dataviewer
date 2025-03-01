<?php

namespace App\Services\Export\PathExporter;

use App\DaViEntity\EntityInterface;
use App\Services\Export\ExportData\PathExport;
use App\Services\Export\ExportRow;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('exporter.path_handler')]
interface PathExporterHandlerInterface {

  public function processEntityPath(ExportRow $row, PathExport $exportPath, EntityInterface $baseEntity): void;

  public function getName(): string;

  public function getLabel(): string;

  public function getDescription(): string;

}