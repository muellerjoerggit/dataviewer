<?php

namespace App\Services\Export\ExportFormatterHandler;

use App\Services\Export\ExportFormatter\ExportFormatterInterface;
use ReflectionClass;
use ReflectionException;

abstract class AbstractExportFormatter implements ExportFormatterInterface {

  protected function getFormatterName(): string {
    try {
      $reflection = new ReflectionClass($this);
    } catch (ReflectionException $exception) {
      return 'NullExportFormatter';
    }

    return $reflection->getShortName();
  }

}