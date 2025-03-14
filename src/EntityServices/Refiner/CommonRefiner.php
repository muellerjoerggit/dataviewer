<?php

namespace App\EntityServices\Refiner;

use App\DaViEntity\EntityInterface;
use App\EntityServices\AvailabilityVerdict\AvailabilityVerdictLocator;
use App\Item\ItemHandler_AdditionalData\AdditionalDataItemHandlerLocator;
use App\Item\ItemHandler_EntityReference\EntityReferenceItemHandlerLocator;
use App\Item\Property\PropertyBuilder;

class CommonRefiner implements RefinerInterface {

  public function __construct(
    private readonly PropertyBuilder $propertyBuilder,
    private readonly EntityReferenceItemHandlerLocator $referenceHandlerLocator,
    private readonly AdditionalDataItemHandlerLocator $additionalDataItemHandlerLocator,
    private readonly AvailabilityVerdictLocator $availabilityVerdictLocator,
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

  public function setAvailability(EntityInterface $entity): void {
    if($entity->getSchema()->hasAvailabilityVerdictService()) {
      $this->availabilityVerdictLocator->getAvailabilityVerdictService($entity::class, $entity->getClient())->setAvailability($entity);
    }
  }

}
