<?php

namespace App\DataCollections;

class Color {

  public function __construct(
    private int $red,
    private int $green,
    private int $blue
  ) {}

  public static function create(int $red, int $green, int $blue): Color {
    return new static($red, $green, $blue);
  }

  public function getDarkerColor(float $percentage): Color {
    return $this->calculateNewColor($percentage);
  }

  public function getBrighterColor(float $percentage): Color {
    return $this->calculateNewColor(-$percentage);
  }

  public function getHexColor(): string {
    return sprintf('#%02x%02x%02x', $this->red, $this->green, $this->blue);
  }

  private function calculateNewColor(float $percentage): Color {
    return new Color(
      $this->calculateColorValue($percentage, $this->red),
      $this->calculateColorValue($percentage, $this->green),
      $this->calculateColorValue($percentage, $this->blue),
    );
  }

  public function __toString(): string {
    return $this->getHexColor();
  }

  private function calculateColorValue(float $percentage, int $color): int {
    $color = $color - ($color * $percentage);
    $color = min($color, 255);
    return max(0, $color);
  }
}