<?php

namespace App\DataCollections\Report_Items;

use App\DataCollections\Report;
use App\DataCollections\ReportElements\ReportElementInterface;
use App\DataCollections\ReportElements\ReportInfoText;
use App\DataCollections\ReportElements_Table\ReportTable;

class ReportModalItem extends AbstractReportItem {

  private array $data;

  public const string ITEM_TYPE = 'modal';

  public function __construct(
    private string $buttonTitle = 'Öffnen',
    private string $modalTitle = '',
  ) {}

  public function add(ReportInfoText | ReportTable $element): static {
    $this->data[] = $element;
    return $this;
  }

  public function toArray(): array {
    return array_merge(
      parent::toArray(),
      [
        'buttonTitle' => $this->buttonTitle,
        'modalTitle' => $this->modalTitle,
        'data' => $this->dataToArray(),
      ]
    );
  }

  private function dataToArray(): array {
    $ret = [];
    foreach ($this->data as $element) {
      if($element instanceof ReportElementInterface && $element->isValid()) {
        $ret[] = $element->getElementData();
      }
    }
    return $ret;
  }

}