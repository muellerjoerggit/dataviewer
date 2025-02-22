<?php

namespace App\Item\ItemHandler_PreRendering;

use App\DaViEntity\DaViEntityManager;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityKey;
use App\Item\ItemHandler_EntityReference\EntityReferenceItemHandlerLocator;
use App\Item\ItemHandler_Formatter\FormatterItemHandlerLocator;
use App\Item\Property\Attribute\OptionItemSettingDefinition;

class EntityReferenceOptionsPreRenderingItemHandler extends EntityReferencePreRenderingItemHandler {

  public function __construct(
    EntityReferenceItemHandlerLocator $referenceHandlerLocator,
    DaViEntityManager $entityManager,
    FormatterItemHandlerLocator $formatterLocator,
  ) {
    parent::__construct($referenceHandlerLocator, $entityManager, $formatterLocator);
  }

  protected function getEntities(EntityInterface $entity, string $property): array {
    $item = $entity->getPropertyItem($property);
    $itemConfiguration = $item->getConfiguration();

    $options = null;
    if($itemConfiguration->hasSetting(OptionItemSettingDefinition::class)) {
      $options = $itemConfiguration->getSetting(OptionItemSettingDefinition::class);
    }

    $entityOverviewOptions = $this->getEntityOverviewOptions($itemConfiguration);

    $entities = [];
    foreach ($item->iterateEntityKeyCollection() as $rawValue => $entityKey) {
      if ($options instanceof OptionItemSettingDefinition && $options->hasOption($rawValue)) {
        $entities[] = $this->buildValueArray($rawValue);
      } elseif ($entityKey instanceof EntityKey) {
        $entities[] = $this->buildEntityArray($entityKey, $entityOverviewOptions);
      } else {
        $entities[] = $this->buildValueArray($rawValue);
      }
    }

    return $entities;
  }

}
