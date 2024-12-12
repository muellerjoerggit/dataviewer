<?php

namespace App\Database\SqlFilter;

class FilterGroup {

  public function __construct(
    private readonly string $groupKey,
    private readonly string $title = '',
    private readonly string $description = '',
  ) {}

  public function getAsArray(): array {
    return [
      'groupKey' => $this->getGroupKey(),
      'title' => $this->getTitle(),
      'description' => $this->getDescription(),
    ];
  }

  public function getGroupKey(): string {
    return $this->groupKey;
  }

  public function getTitle(): string {
    return empty($this->title) ? $this->groupKey : $this->title;
  }

  public function getDescription(): string {
    return $this->description ?? '';
  }

}