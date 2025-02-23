<?php

namespace App\DaViEntity\Refiner;

use App\DaViEntity\EntityInterface;
use App\Item\ItemHandler_AdditionalData\AdditionalDataItemHandlerLocator;
use App\Item\ItemHandler_EntityReference\EntityReferenceItemHandlerLocator;
use App\Item\Property\PropertyBuilder;
use App\Item\ReferenceItemInterface;

class CommonRefiner implements RefinerInterface {

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

      if ($itemConfiguration->hasAdditionalDataHandlerHandler()) {
        $handler = $this->additionalDataItemHandlerLocator->getAdditionalDataItemHandler($itemConfiguration);
        $data = $handler->getValues($entity, $property);
        $item->setRawValues($data);
      }

      if ($itemConfiguration->hasEntityReferenceHandler()) {
        $handler = $this->referenceHandlerLocator->getEntityReferenceHandlerFromItem($itemConfiguration);
        $collection = $handler->buildEntityKeyCollection($entity, $property);
        if($collection) {
          $item->setRawValues($collection);
        }
      }
    }
  }

}
