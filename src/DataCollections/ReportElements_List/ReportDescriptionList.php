<?php

namespace App\DataCollections\ReportElements_List;

use App\DataCollections\ReportElements\ReportElementInterface;

class ReportDescriptionList implements ReportElementInterface {

  private array $data = [];


  public static function create(): ReportDescriptionList {
    return new static;
  }

  public function add(string $term, array | string $messages): ReportDescriptionList {
    $this->data[] = [
      'term' => $term,
      'messages' => is_array($messages) ? $messages : [$messages],
    ];

    return $this;
  }

  public function getElementData(): array {
    return [
      'type' => 'descriptionList',
      'items' => $this->data,
    ];
  }

  public function isValid(): bool {
    return !empty($this->data);
  }

}