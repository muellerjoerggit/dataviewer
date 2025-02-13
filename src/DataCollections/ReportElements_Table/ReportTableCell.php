<?php

namespace App\DataCollections\ReportElements_Table;

use App\DataCollections\Report_Items\ReportBadgeItem;
use App\DataCollections\Report_Items\ReportItemInterface;
use App\DataCollections\Report_Items\ReportModalItem;
use App\DataCollections\Report_Items\ReportResultItem;

class ReportTableCell {

  private const string CELL_TYPE = 'cellType';

  public function __construct(
    private readonly string $columnKey,
    private int | string | float | ReportItemInterface $data,
  ) {}

  public static function create(string $columnKey, int | string | float | ReportItemInterface $data): ReportTableCell {
    return new static($columnKey, $data);
  }

  public function getColumnKey(): string {
    return $this->columnKey;
  }

  public function toArray(): array {
    if(is_scalar($this->data)) {
      return [
        self::CELL_TYPE => 'scalar',
        'value' => $this->data,
      ];
    } elseif($this->data instanceof ReportBadgeItem) {
      return [
        self::CELL_TYPE => 'badge',
        'badge' => $this->data->toArray(),
      ];
    } elseif($this->data instanceof ReportModalItem) {
      return [
        self::CELL_TYPE => 'modal',
        'modal' => $this->data->toArray(),
      ];
    } elseif($this->data instanceof ReportResultItem) {
      return [
        self::CELL_TYPE => 'result',
        'result' => $this->data->toArray(),
      ];
    }

    return [
      'cellType' => 'scalar',
      'value' => '',
    ];
  }

}