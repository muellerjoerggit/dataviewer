<?php

namespace App\DaViEntity\EntityRefiner;

use App\DaViEntity\EntityInterface;
use App\Item\ItemHandler_AdditionalData\AdditionalDataItemHandlerLocator;
use App\Item\ItemHandler_EntityReference\EntityReferenceItemHandlerLocator;
use App\Item\Property\PropertyBuilder;
use App\Item\ReferenceItemInterface;

class CommonEntityRefiner implements EntityRefinerInterface {

  public function __construct(
    private readonly PropertyBuilder $propertyBuilder,
    private readonly EntityReferenceItemHandlerLocator $referenceHandlerLocator,
    private readonly AdditionalDataItemHandlerLocator $additionalDataItemHandlerLocator
  ) {}

  public function refineEntity(EntityInterface $entity): void {
    foreach ($entity->getSchema()->iterateProperties() as $property => $itemConfiguration) {
      if (!$entity->hasPropertyItem($property)) {
        $item = $this->propertyBuilder->createProperty($itemConfiguration, NULL);
        $entity->setPropertyItem($property, $item);
      } else {
        $item = $entity->getPropertyItem($property);
      }

      if ($itemConfiguration->hasAdditionalDataHandler()) {
        $handler = $this->additionalDataItemHandlerLocator->getAdditionalDataItemHandler($itemConfiguration);
        $data = $handler->getValues($entity, $property);
        $item->setRawValues($data);
      }

      if ($itemConfiguration->hasEntityReferenceHandler() && $item instanceof ReferenceItemInterface) {
        $handler = $this->referenceHandlerLocator->getEntityReferenceHandlerFromItem($itemConfiguration);
        foreach ($handler->iterateEntityKeys($entity, $property) as $entityKey) {
          $item->addEntityKey($entityKey);
        }
      }
    }
  }

}
