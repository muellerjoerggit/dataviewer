<?php

namespace App\DataCollections\ReportElements;

class ReportExpandable extends AbstractReportElement {

  public static function create(): ReportExpandable {
    return new static();
  }

  public function getElementData(): array {
    return [];
  }

}