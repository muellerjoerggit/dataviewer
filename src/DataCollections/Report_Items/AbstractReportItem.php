<?php

namespace App\DataCollections\Report_Items;

abstract class AbstractReportItem implements ReportItemInterface {

  public const string ITEM_TYPE = '';

  public function toArray(): array {
    return [
      'itemType' => static::ITEM_TYPE,
    ];
  }

}