<?php

namespace App\DataCollections\ReportElements;

class ReportPreformattedText extends AbstractReportElement {

  public function __construct(
    private string $text,
  ) {}

  public static function create(string $text): ReportPreformattedText {
    return new static($text);
  }

  public function getElementData(): array {
    return [
      'type' => 'preformatted',
      'text' => $this->text
    ];
  }

  public function isValid(): bool {
    return !empty($this->text);
  }

}