<?php

namespace App\Services\Export\ExportFormatter;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ExportFormatterAttribute implements ExportFormatterAttributeInterface {

  public function __construct(
    public readonly string $exportFormatterClass,
  ) {}

  public function getExportFormatterClass(): string {
    return $this->exportFormatterClass;
  }

}