<?php

namespace App\DataCollections\ReportElements;

class ReportSection {

  protected const string TYPE = 'section';

  private array $children = [];

  public function __construct(
    private readonly int $id,
    private string $headline,
  ) {}

  public function getId(): int {
    return $this->id;
  }

  public function getHeadline(): string {
    return $this->headline;
  }

  public function getType(): string {
    return static::TYPE;
  }

}