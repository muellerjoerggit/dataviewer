<?php

namespace App\DataCollections\ReportElements;

class ReportTabs extends AbstractReportElement {

  public static function create(): ReportTabs {
    return new static();
  }

  public function getElementData(): array {
    return [];
  }

}