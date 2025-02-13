<?php

namespace App\DataCollections\ReportElements_Charts;

use App\DataCollections\ReportElements\ReportElementInterface;

class ReportChartBar implements ReportElementInterface {

  private xAxis $xAxis;

  /**
   * @var Bar[]
   */
  private array $bars;
  private bool $verticalGrid = false;
  private bool $horizontalGrid = true;
  private int $gridSpacing = 2;
  private int | float $defaultValue = 0;

  public static function create(xAxis $xAxis, Bar ...$bars): ReportChartBar {
    $chartBar = new static();
    $chartBar->setXAxis($xAxis);

    foreach ($bars as $bar) {
      $chartBar->addBar($bar);
    }
    return $chartBar;
  }

  public function setXAxis(xAxis $xAxis): ReportChartBar {
    $this->xAxis = $xAxis;
    return $this;
  }

  public function addBar(Bar $bar): ReportChartBar {
    $this->bars[$bar->getKey()] = $bar;
    return $this;
  }

  public function hasBar(string $key): bool {
    return isset($this->bars[$key]);
  }

  public function getBar(string $key): Bar {
    return $this->bars[$key];
  }

  public function getElementData(): array {
    return [
      'type' => 'barChart',
      'chartConfig' => $this->buildChartConfig(),
      'chartData' => $this->buildChartData(),
      'xAxis' => $this->xAxis->toArray(),
      'horizontalGrid' => $this->horizontalGrid,
      'verticalGrid' => $this->verticalGrid,
      'gridSpacing' => $this->gridSpacing,
    ];
  }

  public function isValid(): bool {
    return !empty($this->bars);
  }

  public function setDefaultValue(float|int $defaultValue): ReportChartBar {
    $this->defaultValue = $defaultValue;
    return $this;
  }

  private function buildChartConfig(): array {
    $ret = [];
    foreach ($this->bars as $bar) {
      $ret[$bar->getKey()] = [
        'label' => $bar->getLabel(),
        'color' => $bar->getColor()->getHexColor(),
      ];
    }
    return $ret;
  }

  private function buildChartData(): array {
    $data = [];
    $xAxisDataKey = $this->xAxis->getDataKey();
    $dataPoints = $this->xAxis->getDataPoints();
    foreach ($dataPoints as $key => $label) {
      $row = [$xAxisDataKey => $label];
      foreach ($this->bars as $bar) {
        $value = $bar->getData($key);
        $value = $value !== null ? $value : $this->defaultValue;
        $row[$bar->getKey()] = $value;
      }
      $data[] = $row;
    }
    return $data;
  }

  public function addDataPoint(string $key, string $label, array $data): ReportChartBar {
    $this->xAxis->addDataPoint($key, $label);
    foreach ($data as $barKey => $value) {
      if(!$this->hasBar($barKey) || !is_numeric($value)) {
        continue;
      }
      $this->getBar($barKey)->addData($key, $value);
    }
    return $this;
  }

}