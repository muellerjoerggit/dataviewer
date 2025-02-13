<?php

namespace App\DataCollections\ReportElements_Table;

class ReportTableHeader extends AbstractReportTableHeader {

  public const string HEADER_TYPE = 'default';

  protected bool $noWrap = false;

  public static function create(string $key, string $label): ReportTableHeaderInterface {
    return parent::create($key, $label);
  }

  public function setNoWrap(bool $noWrap): ReportTableHeader {
    $this->noWrap = $noWrap;
    return $this;
  }

  public function toArray(): array {
    $ret = parent::toArray();
    $ret['noWrap'] = $this->noWrap;

    return $ret;
  }

}