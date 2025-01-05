<?php

namespace App\Database\BaseQuery;

use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityTypeAttributesReader;
use App\Services\AbstractLocator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class BaseQueryLocator extends AbstractLocator {

  public function __construct(
    private readonly EntityTypeAttributesReader $entityTypeAttributesReader,
    #[AutowireLocator('data_mapper.base_query')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getBaseQuery(string | EntityInterface $entityClass): BaseQueryInterface {
    $baseQuery = $this->entityTypeAttributesReader->getBaseQueryClass($entityClass);
    return $this->get($baseQuery);
  }

}