<?php

namespace App\Services\Export\ExportFormatterHandler;

use App\Services\Export\ExportConfiguration\ExportPropertyConfig;
use App\Services\Export\ExportFormatter\ExportFormatterInterface;

class NullExportFormatter implements ExportFormatterInterface {

  public function isValueValid($value): bool {
    return false;
  }

  public function formatValue($value, ExportPropertyConfig $config): string {
    return '';
  }

  public function getConfigComponent(): array {
    return [];
  }

  public function prepareConfig(array $config): array {
    return [];
  }

}