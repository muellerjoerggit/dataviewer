<?php

namespace App\DaViEntity\OverviewBuilder;

use App\DaViEntity\EntityInterface;

class NullOverviewBuilder implements OverviewBuilderInterface {

  public function buildEntityOverview(EntityInterface $entity, array $options = []): array {
    return [
      'header' => [],
      'data' => [],
    ];
  }

  public function buildExtendedEntityOverview(EntityInterface $entity, array $options = []): array {
    return [
      'header' => [],
      'data' => [],
    ];
  }

}