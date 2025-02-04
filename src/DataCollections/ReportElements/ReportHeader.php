<?php

namespace App\DataCollections\ReportElements;

class ReportHeader implements ReportElementInterface {

  public function __construct(
    private string $headline,
    private string $description = '',
  ) {}

  public function getHeadline(): string {
    return $this->headline;
  }

  public function setHeadline(string $headline): ReportHeader {
    $this->headline = $headline;
    return $this;
  }

  public function getDescription(): string {
    return $this->description;
  }

  public function setDescription(string $description): ReportHeader {
    $this->description = $description;
    return $this;
  }

  public function getElementData(): array {
    return [
      'type' => 'header',
      'headline' => $this->getHeadline(),
      'description' => $this->getDescription(),
    ];
  }

}