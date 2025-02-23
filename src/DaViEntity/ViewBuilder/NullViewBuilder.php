<?php

namespace App\DaViEntity\ViewBuilder;

use App\DaViEntity\EntityInterface;

class NullViewBuilder implements ViewBuilderInterface {

  public function preRenderEntity(EntityInterface $entity): array {
    return [
      'entityKey' => $entity->getFirstEntityKeyAsString(),
      'label' => 'kein Titel',
      'entityOverview' => [
        'header' => [],
        'data' => [],
      ],
      'extEntityOverview' => [
        'header' => [],
        'data' => [],
      ],
      'properties' => [],
      'logsByLevel' => [],
      'entityActions' => [],
    ];
  }

}