<?php

namespace App\Services\Export\ExportFormatter;

use App\Services\Export\ExportConfiguration\ExportPropertyConfig;

interface ExportFormatterInterface {

  public function isValueValid($value): bool;

  public function formatValue($value, ExportPropertyConfig $config): string;

  public function getConfigComponent(): array;

  public function prepareConfig(array $config): array;

}