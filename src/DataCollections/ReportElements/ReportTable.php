<?php

namespace App\DataCollections\ReportElements;

class ReportTable implements ReportElementInterface {

	private array $tableData = [];
	private array $header = [];
	private array $modalColumns = [];
	private array $iconColumns = [];
	private string $emptyResult = 'keine Ergebnisse';
	private string $modalTitle = '';
	private string $titleModalButton = 'Öffnen';
	private bool $firstColumnSticky = false;
	private bool $noWrap = false;

	public function __construct(array $header, array $tableData) {
		$this->tableData = $tableData;
		$this->header = $header;
	}

	public function getElementData(): array {
		return [
			'type' => 'table',
			'header' => $this->header,
			'table' => $this->tableData,
			'emptyResult' => $this->emptyResult,
			'modalColumns' => $this->modalColumns,
			'iconColumns' => $this->iconColumns,
			'modalTitle' => $this->modalTitle,
			'titleModalButton' => $this->titleModalButton,
			'firstColumnSticky' => $this->firstColumnSticky,
			'noWrap' => $this->noWrap
		];
	}

	public function setNoWrap(bool $noWrap): ReportTable {
		$this->noWrap = $noWrap;
		return $this;
	}

	public function setHeader(array $header): ReportTable {
		$this->header = $header;
		return $this;
	}

	public function setTableData(array $tableData): ReportTable	{
		$this->tableData = $tableData;
		return $this;
	}

	public function setModalColumns(array $modalColumns): ReportTable {
		$this->modalColumns = $modalColumns;
		return $this;
	}

	public function setIconColumns(array $iconColumns): ReportTable {
		$this->iconColumns = $iconColumns;
		return $this;
	}

	public function setEmptyResult(string $emptyResult): ReportTable {
		$this->emptyResult = $emptyResult;
		return $this;
	}

	public function setModalTitle(string $modalTitle): ReportTable	{
		$this->modalTitle = $modalTitle;
		return $this;
	}

	public function setTitleModalButton(string $titleModalButton): ReportTable	{
		$this->titleModalButton = $titleModalButton;
		return $this;
	}

	public function setFirstColumnSticky(bool $firstColumnSticky): ReportTable	{
		$this->firstColumnSticky = $firstColumnSticky;
		return $this;
	}


}
