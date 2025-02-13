<?php

namespace App\DataCollections\ReportElements_Charts;

use App\DataCollections\Color;

class Bar {

  private string $label;
  private Color $color;
  private array $data;

  public function __construct(
    private readonly string $key,
  ) {}

  public static function create(string $key, string $label, Color $color): Bar {
    $bar = new static($key);
    if(!empty($label)) {
      $bar->setLabel($label);
    }
    $bar->setColor($color);
    return $bar;
  }

  public function getLabel(): string {
    return $this->label ?? $this->key;
  }

  public function setLabel(string $label): Bar {
    $this->label = $label;
    return $this;
  }

  public function getColor(): Color {
    return $this->color;
  }

  public function setColor(Color $color): Bar {
    $this->color = $color;
    return $this;
  }

  public function getKey(): string {
    return $this->key;
  }

  public function addData(string $key, int | float $value): void {
    $this->data[$key] = $value;
  }

  public function getData(string $key): int | float | null {
    return $this->data[$key] ?? null;
  }

}