<?php

namespace App\DataCollections\ReportElements_Table;

abstract class AbstractReportTableHeader implements ReportTableHeaderInterface {

  public const string HEADER_TYPE = 'default';

  public function __construct(
    protected string $key,
    protected string $label,
  ) {}

  public static function create(string $key, string $label): ReportTableHeaderInterface {
    return new static($key, $label);
  }

  public function getKey(): string {
    return $this->key;
  }

  public function setKey(string $key): ReportTableHeaderInterface {
    $this->key = $key;
    return $this;
  }

  public function getLabel(): string {
    return $this->label;
  }

  public function setLabel(string $label): ReportTableHeaderInterface {
    $this->label = $label;
    return $this;
  }

  public function toArray(): array {
    return [
      'headerType' => static::HEADER_TYPE,
      'key' => $this->key,
      'label' => $this->label,
    ];
  }

}