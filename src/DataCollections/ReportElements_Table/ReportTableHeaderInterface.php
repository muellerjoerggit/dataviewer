<?php

namespace App\DataCollections\ReportElements_Table;

interface ReportTableHeaderInterface {

  public function getKey(): string;

  public function setKey(string $key): ReportTableHeaderInterface;

  public function getLabel(): string;

  public function setLabel(string $label): ReportTableHeaderInterface;

  public function toArray(): array;

}