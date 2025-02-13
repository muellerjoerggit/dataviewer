<?php

namespace App\DataCollections\ReportElements_List;

use App\DataCollections\ReportElements\ReportElementInterface;

class ReportOrderedList implements ReportElementInterface {

  protected array $data = [];

  public static function create(): ReportOrderedList {
    return new static;
  }

  public function add(string $item): ReportOrderedList {
    $this->data[] = $item;

    return $this;
  }

  public function getElementData(): array {
    return [
      'type' => 'orderedList',
      'items' => $this->data,
    ];
  }

  public function isValid(): bool {
    return !empty($this->data);
  }

}