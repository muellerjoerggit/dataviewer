<?php

namespace App\Item\ItemHandler_PreRendering;

use App\DaViEntity\DaViEntityManager;
use App\DaViEntity\EntityInterface;
use App\Item\ItemHandler_EntityReference\EntityReferenceItemHandlerLocator;
use App\Item\ItemHandler_ValueFormatter\ValueFormatterItemHandlerLocator;

class EntityReferenceOptionsPreRenderingItemHandler extends EntityReferencePreRenderingItemHandler {

  public function __construct(
    EntityReferenceItemHandlerLocator $referenceHandlerLocator,
    DaViEntityManager $entityManager,
    ValueFormatterItemHandlerLocator $formatterLocator,
  ) {
    parent::__construct($referenceHandlerLocator, $entityManager, $formatterLocator);
  }

  protected function getEntities(EntityInterface $entity, string $property): array {
    $item = $entity->getPropertyItem($property);
    $itemConfiguration = $item->getConfiguration();
    $handler = $this->referenceHandlerLocator->getEntityReferenceHandlerFromItem($itemConfiguration);
    $options = $item->getConfiguration()->getSetting('options', []);
    $entityOverviewOptions = $this->getEntityOverviewOptions($itemConfiguration);

    $entities = [];
    foreach ($item->iterateValues() as $value) {
      if (in_array($value, $options)) {
        $entities[] = $this->buildValueArray($value);
      }

      $entityKey = $handler->buildEntityKeys($value, $itemConfiguration, $entity->getClient());
      $entities[] = $this->buildEntityArray($entityKey, $entityOverviewOptions);
    }

    return $entities;
  }

}
