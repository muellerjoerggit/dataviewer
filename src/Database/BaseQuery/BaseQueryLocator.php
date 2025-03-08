<?php

namespace App\Database\BaseQuery;

use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\Services\AbstractLocator;
use App\Services\ClientService;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class BaseQueryLocator extends AbstractLocator {

  public function __construct(
    private readonly EntityTypeSchemaRegister $entityTypeSchemaRegister,
    private readonly ClientService $clientService,
    #[AutowireLocator('data_mapper.base_query')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getBaseQuery(EntitySchema $entitySchema, string $client): BaseQueryInterface {
    $version = $this->clientService->getClientVersion($client);
    $class = $entitySchema->getBaseQueryClass($version);

    if($this->has($class)) {
      return $this->get($class);
    } else {
      return $this->get(CommonBaseQuery::class);
    }
  }

  public function getBaseQueryFromEntityClass(string $entityClass, string $client): BaseQueryInterface {
    $entitySchema = $this->entityTypeSchemaRegister->getSchemaFromEntityClass($entityClass);
    return $this->getBaseQuery($entitySchema, $client);
  }

}