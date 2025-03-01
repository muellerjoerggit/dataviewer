<?php

namespace App\Services\Export\ExportConfiguration;

interface ExportPathConfigurationInterface {

  public function getPathExporterClass(): string;

  public function hasPath(): bool;

  public function getPath(): array;

}