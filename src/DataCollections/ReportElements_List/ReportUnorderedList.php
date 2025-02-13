<?php

namespace App\DataCollections\ReportElements_List;

use App\DataCollections\ReportElements\ReportElementInterface;

class ReportUnorderedList extends ReportOrderedList implements ReportElementInterface {

  public function getElementData(): array {
    $ret = parent::getElementData();
    $ret['type'] = 'unorderedList';

    return $ret;
  }

}