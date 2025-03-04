<?php

namespace App\EntityTypes\NullEntity;

use App\Database\SqlFilter\FilterContainer;
use App\DataCollections\EntityList;
use App\DaViEntity\DaViEntityManager;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityKey;
use App\EntityServices\Repository\RepositoryInterface;

class NullRepository implements RepositoryInterface {

  public function __construct(
    private readonly DaViEntityManager $entityManager,
  ) {}

  public function loadEntityData(FilterContainer $filterContainer, array $options = []): array {
    return [];
  }

  public function loadMultipleEntities(FilterContainer $filterContainer, array $options = []): array {
    return [];
  }

  public function loadEntityByEntityKey(EntityKey $entityKey): EntityInterface {
    return $this->entityManager->createNullEntity();
  }

  public function getEntityList(FilterContainer $filterContainer): EntityList {
    return new EntityList();
  }

}