<?php

namespace App\Services\Export;

class ExportRow {

  public function __construct(
    private readonly string $key,
  ) {}

  public function getKey(): string {
    return $this->key;
  }

}