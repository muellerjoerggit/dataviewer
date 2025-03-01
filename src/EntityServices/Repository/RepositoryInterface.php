<?php

namespace App\EntityServices\Repository;

use App\Database\SqlFilter\FilterContainer;
use App\DataCollections\EntityList;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityKey;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('entity_management.entity_repository')]
interface RepositoryInterface {

  public function loadEntityData(FilterContainer $filterContainer, array $options = []): array;

  public function loadMultipleEntities(FilterContainer $filterContainer, array $options = []): array;

  public function loadEntityByEntityKey(EntityKey $entityKey): EntityInterface;

  public function getEntityList(FilterContainer $filterContainer): EntityList;

}