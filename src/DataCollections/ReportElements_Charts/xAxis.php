<?php

namespace App\DataCollections\ReportElements_Charts;

class xAxis {

  private bool $tickLine = false;
  private int $tickMargin = 10;
  private bool $axisLine = true;
  private array $dataPoints = [];

  public function __construct(
    private readonly string $dataKey,
  ) {}

  public static function create(string $dataKey, array $dataPoints = []): xAxis {
    $xAxis = new static($dataKey);
    $xAxis->setDataPoints($dataPoints);
    return $xAxis;
  }

  public function isTickLine(): bool {
    return $this->tickLine;
  }

  public function getTickMargin(): int {
    return $this->tickMargin;
  }

  public function isAxisLine(): bool {
    return $this->axisLine;
  }

  public function getDataKey(): string {
    return $this->dataKey;
  }

  public function getDataPoints(): array {
    return $this->dataPoints;
  }

  public function setDataPoints(array $dataPoints): xAxis {
    $this->dataPoints = $dataPoints;
    return $this;
  }

  public function addDataPoint(string $key, string $label): xAxis {
    $this->dataPoints[$key] = $label;
    return $this;
  }

  public function toArray(): array {
    return [
      'dataKey' => $this->dataKey,
      'tickLine' => $this->tickLine,
      'tickMargin' => $this->tickMargin,
      'axisLine' => $this->axisLine,
    ];
  }

}