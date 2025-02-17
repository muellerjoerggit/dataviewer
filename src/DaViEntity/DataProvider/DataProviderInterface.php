<?php

namespace App\DaViEntity\DataProvider;

use App\Database\SqlFilter\FilterContainer;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('entity_management.entity_data_provider')]
interface DataProviderInterface {

  public function fetchEntityData(string $entityClass, FilterContainer $filters, array $options = []): array;

}