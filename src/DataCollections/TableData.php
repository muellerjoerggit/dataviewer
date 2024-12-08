<?php

namespace App\DataCollections;

class TableData implements ArrayInterface {

	private array $header = [];
	private array $rows = [];

	public function __construct(array $header, array $rows)	{
		$this->header = $header;
		$this->rows = $rows;
	}

	public function getHeader(): array {
		return $this->header;
	}

	public function getRows(): array {
		return $this->rows;
	}

	public function toArray(): array {
		return $this->rows;
	}

	public function toString(): string {
		return implode(
			', ',
			array_map(
				function($row) {
					return implode(', ', $row);
				},
				$this->rows
			)
		);
	}

	public function __toString(): string {
		return $this->toString();
	}

}
