<?php

namespace App\DaViEntity\Search;

use App\DaViEntity\EntityInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('entity_management.entity_list_search')]
interface SearchInterface {

  public function getEntityListFromSearchString(string | EntityInterface $entityClass, string $client, string $searchString): array;

}