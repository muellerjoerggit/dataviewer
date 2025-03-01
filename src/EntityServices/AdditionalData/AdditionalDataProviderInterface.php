<?php

namespace App\EntityServices\AdditionalData;

use App\DaViEntity\EntityInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Loading additional data from different sources. An entity can have several AdditionalDataProviders.
 */
#[AutoconfigureTag('entity_management.additional_data_provider')]
interface AdditionalDataProviderInterface {

  public function loadData(EntityInterface $entity): void;

}