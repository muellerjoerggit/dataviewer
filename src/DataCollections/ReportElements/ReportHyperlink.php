<?php

namespace App\DataCollections\ReportElements;

class ReportHyperlink extends AbstractReportElement {

  public static function create(): ReportHyperlink {
    return new static();
  }

  public function getElementData(): array {
    return [];
  }

}