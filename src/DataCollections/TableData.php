<?php

namespace App\DataCollections;

class TableData implements ArrayInterface {

  private array $header = [];

  private array $rows = [];

  public function __construct(array $header, array $rows) {
    $this->header = $header;
    $this->rows = $rows;
  }

  public static function create(array $header, array $rows): static {
    return new static($header, $rows);
  }

  public static function createEmptyTableData(): static {
    return new static([], []);
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

  public function __toString(): string {
    return $this->toString();
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

}
