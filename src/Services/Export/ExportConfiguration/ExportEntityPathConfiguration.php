<?php

namespace App\Services\Export\ExportConfiguration;

use Generator;

class ExportEntityPathConfiguration {

  private array $properties = [];

  public function __construct(
    private readonly array $path = []
  ) {}

  public function getPath(): array {
    return $this->path;
  }

  public function hasPath(): bool {
    return !empty($this->path);
  }

  public function addPropertyConfig(ExportPropertyConfig $exportPropertyConfig): static {
    $this->properties[] = $exportPropertyConfig;
    return $this;
  }

  /**
   * @return Generator<ExportPropertyConfig[]>
   */
  public function iteratePropertyConfigs(): Generator {
    foreach ($this->properties as $property) {
      yield $property;
    }
  }

}