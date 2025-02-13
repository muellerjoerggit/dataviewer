<?php

namespace App\Feature;

use App\Feature\Features\NullReportFeature;

class FeatureRegister {

  private array $features = [];

  public function __construct(
    FeatureReader $reader,
  ) {
    $this->features = $reader->getFeatures();
  }

  public function getFeature(string $feature) {
    return $this->features[$feature] ?? $this->getNullFeature();
  }

  public function getFeatureClass(string $feature) {
    return $this->getFeature($feature)['class'];
  }

  public function getDescriptionByFeatureClass(string $featureController): string {
    $features = array_column($this->features, 'feature', 'class');
    $featureName = $features[$featureController] ?? '';
    return $this->getDescription($featureName);
  }

  public function getDescription(string $feature): string {
    return $this->features[$feature]['description'] ?? '';
  }

  public function getFeatureList(): array {
    return array_map(function($feature) {
      unset($feature['class']);
      return $feature;
    }, $this->features);
  }

  private function getNullFeature(): array {
    return [
      'feature' => 'NullReport',
      'label' => 'NullReportFeature',
      'description' => 'Fehler: Feature nicht gefunden',
      'class' => NullReportFeature::class,
    ];
  }

}