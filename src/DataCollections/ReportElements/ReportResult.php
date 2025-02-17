<?php

namespace App\DataCollections\ReportElements;

class ReportResult implements ReportElementInterface {

	public const RESULT_ICON_UNKNOWN = 1;
	public const RESULT_ICON_SUCCESS = 2;
	public const RESULT_ICON_FAILED = 3;

	private string $term = '';
	private	string $description = '';
	private int $resultIcon = self::RESULT_ICON_UNKNOWN;
	private string $termTooltip = '';
	private string $resultTooltip = '';

    public function getElementData(): array {
		return [
			'type' => 'result',
			'term' => $this->term,
			'description' => $this->description,
			'termTooltip' => $this->termTooltip,
			'result' => $this->resultIcon,
			'resultTooltip' => $this->resultTooltip
		];
    }

	public function getDescription(): string {
		return $this->description;
	}

	public function setDescription(string $description): ReportResult {
		$this->description = $description;
		return $this;
	}

	public function getResultIcon(): int {
		return $this->resultIcon;
	}

	public function setSuccessOrFailedIcon(bool $result): ReportResult{
		$this->setResultIcon($result ? self::RESULT_ICON_SUCCESS : self::RESULT_ICON_FAILED);
		return $this;
	}

	public function setResultIcon(int $resultIcon): ReportResult {
		$this->resultIcon = $resultIcon;
		return $this;
	}

	public function getResultTooltip(): string {
		return $this->resultTooltip;
	}

	public function setResultTooltip(string $resultTooltip): ReportResult {
		$this->resultTooltip = $resultTooltip;
		return $this;
	}

	public function getTerm(): string {
		return $this->term;
	}

	public function setTerm(string $term): ReportResult {
		$this->term = $term;
		return $this;
	}

	public function getTermTooltip(): string {
		return $this->termTooltip;
	}

	public function setTermTooltip(string $termTooltip): ReportResult {
		$this->termTooltip = $termTooltip;
		return $this;
	}

  public function isValid(): bool {
    return true;
  }

}
