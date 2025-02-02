<?php

namespace App\Services\Export\ExportFormatterHandler;

use App\Services\Export\ExportConfiguration\ExportPropertyConfig;
use App\Services\Export\ExportFormatter\ExportFormatterInterface;
use App\Services\HtmlService;

class HtmlExportFormatter extends AbstractExportFormatter {

  public function __construct(
    private readonly HtmlService $htmlService,
  ) {}

  public function isValueValid($value): bool {
    return is_string($value);
  }

  public function formatValue($value, ExportPropertyConfig $config): string {
    if(!$this->isValueValid($value)) {
      return '';
    }

    return $this->htmlService->htmlToText($value);
  }

  public function getConfigComponent(): array {
    return [
      'component' => 'Checkbox',
      'name' => $this->getFormatterName(),
      'label' => 'Html entfernen',
      'description' => 'Entfernt das gesamte HTML aus den Daten',
      'config' => []
    ];
  }

  public function prepareConfig(array $config): array {
    return $config;
  }

}