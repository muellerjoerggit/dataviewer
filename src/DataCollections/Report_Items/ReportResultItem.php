<?php

namespace App\DataCollections\Report_Items;

use App\DataCollections\ReportElements_List\ReportResultList;

class ReportResultItem extends AbstractReportItem {

  public const string ITEM_TYPE = 'result';

  public const int RESULT_ICON_UNKNOWN = 1;
  public const int RESULT_ICON_SUCCESS = 2;
  public const int RESULT_ICON_FAILED = 3;

  private string $term = '';
  private	string $description = '';
  private int $resultIcon = self::RESULT_ICON_UNKNOWN;
  private string $termTooltip = '';
  private string $resultTooltip = '';

  public static function create(int $resultIcon, string $term = ''): ReportResultItem {
    $item = new static();
    $item->setResultIcon($resultIcon);
    $item->setTerm($term);
    return $item;
  }

  public static function createFull(int $resultIcon, string $term, string $description, string $termTooltip, string $resultTooltip): ReportResultItem {
    $item = new static();
    $item
      ->setResultIcon($resultIcon)
      ->setTerm($term)
      ->setDescription($description)
      ->setTermTooltip($termTooltip)
      ->setResultTooltip($resultTooltip);
    return $item;
  }

  public function getDescription(): string {
    return $this->description;
  }

  public function setDescription(string $description): ReportResultItem {
    $this->description = $description;
    return $this;
  }

  public function getResultIcon(): int {
    return $this->resultIcon;
  }

  public function setSuccessOrFailedIcon(bool $result): ReportResultItem{
    $this->setResultIcon($result ? self::RESULT_ICON_SUCCESS : self::RESULT_ICON_FAILED);
    return $this;
  }

  public function setResultIcon(int $resultIcon): ReportResultItem {
    $this->resultIcon = $resultIcon;
    return $this;
  }

  public function getResultTooltip(): string {
    return $this->resultTooltip;
  }

  public function setResultTooltip(string $resultTooltip): ReportResultItem {
    $this->resultTooltip = $resultTooltip;
    return $this;
  }

  public function getTerm(): string {
    return $this->term;
  }

  public function setTerm(string $term): ReportResultItem {
    $this->term = $term;
    return $this;
  }

  public function getTermTooltip(): string {
    return $this->termTooltip;
  }

  public function setTermTooltip(string $termTooltip): ReportResultItem {
    $this->termTooltip = $termTooltip;
    return $this;
  }

  public function toArray(): array {
    return array_merge(
      parent::toArray(),
      [
        'term' => $this->term,
        'description' => $this->description,
        'termTooltip' => $this->termTooltip,
        'result' => $this->resultIcon,
        'resultTooltip' => $this->resultTooltip
      ]
    );
  }

}