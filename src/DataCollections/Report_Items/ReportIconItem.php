<?php

namespace App\DataCollections\Report_Items;

class ReportIconItem extends AbstractReportItem {

  public const string ITEM_SUCCESS = 'success';
  public const string ITEM_QUESTION_MARK = 'unknown';
  public const string ITEM_FAILED = 'failed';
  public const string ITEM_TRASH = 'trash';
  public const string ITEM_EYE = 'eye';

  public function __construct(
    private readonly string $icon,
  ) {}

  public static function createSuccess(): ReportIconItem {
    return new static(self::ITEM_SUCCESS);
  }

  public static function createFailed(): ReportIconItem {
    return new static(self::ITEM_FAILED);
  }

  public function getIcon(): string {
    return $this->icon;
  }

}