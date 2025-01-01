<?php

namespace App\DaViEntity;

use Generator;

/**
 *
 */
class UniqueIdentifier {

  private array $identifiers = [];

  public function addIdentifier(string $identifier, string | int $value): UniqueIdentifier {
    $this->identifiers[$identifier] = $value;
    return $this;
  }

  public function getIdentifiers(): array {
    return $this->identifiers;
  }

  public function isCompositeIdentifier(): bool {
    return count($this->getIdentifierKeys()) > 1;
  }

  public function getIdentifierKeys(): array {
    return array_keys($this->identifiers);
  }

  public function getIdentifierValues(): array {
    return array_values($this->identifiers);
  }

  public function getAsString(): string {
    $keys = implode('+', $this->getIdentifierKeys());
    $values = implode('+', array_values($this->identifiers));
    return "$keys::$values";
  }

  public function iterateIdentifier(): Generator {
    foreach ($this->identifiers as $key => $value) {
      yield $key => $value;
    }
  }

}