<?php

namespace App\Item\ItemHandler_AdditionalData;

use App\DataCollections\TableData;
use App\DaViEntity\EntityInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('additional_data_item_handler')]
interface AdditionalDataItemHandlerInterface {

  public function getValues(EntityInterface $entity, string $property): TableData|array;

}
