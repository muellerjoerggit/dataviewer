<?php

namespace App\Item\ItemHandler_PreRendering;

use App\DaViEntity\DaViEntityManager;
use App\DaViEntity\EntityInterface;
use App\Item\ItemHandler_EntityReference\EntityReferenceItemHandlerLocator;
use App\Item\ItemHandler_Formatter\FormatterItemHandlerLocator;

class EntityReferenceOverviewPreRenderingItemHandler extends EntityReferencePreRenderingItemHandler {

  public function __construct(EntityReferenceItemHandlerLocator $referenceHandlerLocator, DaViEntityManager $entityManager, FormatterItemHandlerLocator $formatterLocator) {
    parent::__construct($referenceHandlerLocator, $entityManager, $formatterLocator);
  }

  public function getComponentPreRenderArray(EntityInterface $entity, string $property): array {
    $ret = parent::getComponentPreRenderArray($entity, $property);
    $ret['component'] = 'EntityOverviewItem';

    return $ret;
  }

}
