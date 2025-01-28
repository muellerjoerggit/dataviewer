<?php

namespace App\Services\Export\ExportData;

class ExportDataGroup {

  private array $data = [];

  public function __construct(
    private readonly string $key,
    private readonly string $label
  ) {}

  public function addData(mixed $data): static {
    if(is_array($data)) {
      $this->data = array_merge($this->data, $data);
    } elseif(is_scalar($data)) {
      $this->data[] = $data;
    }

    return $this;
  }

  public function getData(): array {
    return $this->data;
  }

  public function getLabel(): string {
    return $this->label;
  }

  public function getKey(): string {
    return $this->key;
  }

}