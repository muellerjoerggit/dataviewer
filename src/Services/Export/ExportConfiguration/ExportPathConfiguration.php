<?php

namespace App\Services\Export\ExportConfiguration;

class ExportPathConfiguration implements ExportPathConfigurationInterface {

  public function __construct(
    private readonly array $path = [],
    private readonly string $exporterClass,
  ) {}

  public function getPath(): array {
    return $this->path;
  }

  public function hasPath(): bool {
    return !empty($this->path);
  }

  public function getPathExporterClass(): string {
    return $this->exporterClass;
  }

}