<?php

namespace App\DaViEntity\SimpleSearch;

use App\DaViEntity\EntityInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('entity_management.simple_search')]
interface SimpleSearchInterface {

  public function getEntityListFromSearchString(string | EntityInterface $entityClass, string $client, string $searchString): array;

}