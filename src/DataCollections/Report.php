<?php

namespace App\DataCollections;

use App\DataCollections\ReportElements\ReportElementInterface;
use App\DataCollections\ReportElements\ReportHeader;
use App\DataCollections\ReportElements\ReportResult;
use App\DataCollections\ReportElements\ReportTable;

class Report {

	public const int RESULT_ICON_UNKNOWN = 1;
	public const int RESULT_ICON_SUCCESS = 2;
	public const int RESULT_ICON_FAILED = 3;

	private array $reportBody = [];
	private ReportHeader $reportHeader;
	private bool $tableOfContent = false;

	public function getReportBody(): array {
		$ret = [];

		foreach ($this->reportBody as $sectionId => $sectionData) {
			$ret[$sectionId]['type'] = $sectionData['type'];
			$ret[$sectionId]['headline'] = $sectionData['headline'];
			$ret[$sectionId]['anker'] = $sectionData['anker'];
			$ret[$sectionId]['children'] = [];
			foreach ($sectionData['children'] as $element) {
				if($element instanceof ReportElementInterface) {
					$ret[$sectionId]['children'][] = $element->getElementData();
				} elseif(is_array($element)) {
					$ret[$sectionId]['children'][] = $element;
				}
			}
		}

		return $ret;
	}

	public function getReportHeader(): ReportHeader {
		return $this->reportHeader;
	}

	public function hasTableOfContent(): bool {
		return $this->tableOfContent;
	}

	public function setTableOfContent(bool $tableOfContent): Report {
		$this->tableOfContent = $tableOfContent;
		return $this;
	}

	public function createReportHeader(string $name, $description): Report {
		$this->reportHeader = new ReportHeader($name, $description);

		return $this;
	}

	public function addSection(string $headline): int {
		$this->reportBody[] = [
			'type' => 'section',
			'headline' => $headline,
			'children' => []
		];

		$id = array_key_last($this->reportBody);
		$this->reportBody[$id]['anker'] = 'section' . $id;

		return $id;
	}

	public function addSubSection(string $headline): int {
		$this->reportBody[] = [
			'type' => 'subsection',
			'headline' => $headline,
			'children' => []
		];
		$id = array_key_last($this->reportBody);
		$this->reportBody[$id]['anker'] = 'subSection' . $id;

		return $id;
	}

	public function addElement(int $sectionId, string $term, string | array $messages = []): Report {
		if(!isset($this->reportBody[$sectionId])) {
			return $this;
		}

		if(!is_array($messages)) {
			$messages = [$messages];
		}

		$this->reportBody[$sectionId]['children'][] = [
			'type' => 'element',
			'term' => $term,
			'messages' => $messages
		];

		return $this;
	}

	/**
	 * @deprecated
	 * @param int $sectionId
	 * @param array $header
	 * @param array $table
	 * @param array $columnsIcon
	 * @param array $columnsModal
	 * @param string $emptyResult
	 * @param string $modalTitle
	 * @param string $titleModalButton
	 * @param bool $firstColumnSticky
	 * @return $this
	 */
	public function addTable(
		int $sectionId,
		array $header,
		array $table,
		array $columnsIcon = [],
		array $columnsModal = [],
		string $emptyResult = 'keine Ergebnisse',
		string $modalTitle = '',
		string $titleModalButton = 'Öffnen',
		bool $firstColumnSticky = false
	): Report {
		if(!isset($this->reportBody[$sectionId])) {
			return $this;
		}

		$this->reportBody[$sectionId]['children'][] = [
			'type' => 'table',
			'header' => $header,
			'table' => $table,
			'emptyResult' => $emptyResult,
			'modalColumns' => $columnsModal,
			'iconColumns' => $columnsIcon,
			'modalTitle' => $modalTitle,
			'titleModalButton' => $titleModalButton,
			'firstColumnSticky' => $firstColumnSticky
		];

		return $this;
	}

	public function addInfotext(int $sectionId, string $text): Report {
		if(!isset($this->reportBody[$sectionId])) {
			return $this;
		}

		$this->reportBody[$sectionId]['children'][] = [
			'type' => 'infotext',
			'message' => $text
		];

		return $this;
	}

	/**
	 * @deprecated
	 */
	public function addResult(int $sectionId, string $term, string $description = '', int $resultIcon = self::RESULT_ICON_UNKNOWN, string $termTooltip = '', string $resultTooltip = ''): Report {
		if(!isset($this->reportBody[$sectionId])) {
			return $this;
		}

		$this->reportBody[$sectionId]['children'][] = self::buildResult($term, $description, $resultIcon, $termTooltip, $resultTooltip);

		return $this;
	}

	/**
	 * @deprecated
	 */
	public static function buildResult(string $term, string $description = '', int $resultIcon = self::RESULT_ICON_UNKNOWN, string $termTooltip = '', string $resultTooltip = ''): array {
		return [
			'type' => 'result',
			'term' => $term,
			'description' => $description,
			'termTooltip' => $termTooltip,
			'result' => $resultIcon,
			'resultTooltip' => $resultTooltip
		];
	}

	public function addTableElement(int $sectionId, array $header, array $table): ReportTable {
		$reportTable = new ReportTable($header, $table);
		$this->reportBody[$sectionId]['children'][] = $reportTable;
		return $reportTable;
	}

	public function addResultElement(int $sectionId): ReportResult {
		$reportResult = new ReportResult();
		$this->reportBody[$sectionId]['children'][] = $reportResult;
		return $reportResult;
	}

	public function getAsArray(): array {
		return [
			'header' => $this->getReportHeader(),
			'body' => $this->getReportBody(),
			'tableOfContent' =>	$this->hasTableOfContent()
		];
	}

}
