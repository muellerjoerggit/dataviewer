<?php

namespace App\DataCollections\ReportElements_Table;

use App\DataCollections\ReportElements\ReportElementInterface;

class ReportTable implements ReportElementInterface {

	private array $rows = [];
	private array $header = [];
	private array $modalColumns = [];
	private array $iconColumns = [];
	private string $emptyResult = 'keine Ergebnisse';
	private string $modalTitle = '';
	private string $titleModalButton = 'Öffnen';
	private bool $firstColumnSticky = false;

	public function getElementData(): array {
		return [
			'type' => 'table',
			'header' => $this->headerToArray(),
			'rows' => $this->rowsToArray(),
			'emptyResult' => $this->emptyResult,
			'modalColumns' => $this->modalColumns,
			'iconColumns' => $this->iconColumns,
			'modalTitle' => $this->modalTitle,
			'titleModalButton' => $this->titleModalButton,
			'firstColumnSticky' => $this->firstColumnSticky,
		];
	}

  public static function create(): ReportTable {
    return new static();
  }

  private function headerToArray(): array {
    $ret = [];
    foreach ($this->header as $header) {
      $ret[$header->getKey()] = $header->toArray();
    }
    return $ret;
  }

  private function rowsToArray(): array {
    $ret = [];
    foreach ($this->rows as $row) {
      $ret[] = $row->toArray();
    }
    return $ret;
  }

  public function addHeader(ReportTableHeaderInterface ...$headers): ReportTable {
    foreach ($headers as $header) {
      $this->header[$header->getKey()] = $header;
    }
    return $this;
  }

  public function addRows(ReportTableRow ...$rows): ReportTable {
    foreach ($rows as $row) {
      $this->rows[] = $row;
    }
    return $this;
  }

  public function setFirstColumnSticky(bool $firstColumnSticky): ReportTable {
    $this->firstColumnSticky = $firstColumnSticky;
    return $this;
  }

  public function isValid(): bool {
    return !empty($this->rows) && !empty($this->header);
  }

}
