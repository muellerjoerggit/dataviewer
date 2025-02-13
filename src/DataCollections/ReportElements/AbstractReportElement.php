<?php

namespace App\DataCollections\ReportElements;

abstract class AbstractReportElement implements ReportElementInterface {

  public function isValid(): bool {
    return false;
  }

}