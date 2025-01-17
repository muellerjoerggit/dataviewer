<?php

namespace App\Database;

use App\DaViEntity\EntityDataMapperInterface;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\Services\ClientService;
use App\Services\Version\VersionInformation;
use App\Services\Version\VersionService;

class ColumnsService {

  public function __construct(
    private readonly VersionService $versionService,
    private readonly EntityTypeSchemaRegister $entityTypeSchemaRegister,
    private readonly ClientService $clientService,
  ) {}

  public function getColumns(string | EntityInterface $entityTypeClass, string $client, array $options = []): array {
    $schema = $this->entityTypeSchemaRegister->getSchemaFromEntityClass($entityTypeClass);

    if (
      $options[EntityDataMapperInterface::OPTION_WITH_COLUMNS]
      && empty($options[EntityDataMapperInterface::OPTION_COLUMNS])
    ) {
      $columns = $schema->getColumns();
    } elseif (
      $options[EntityDataMapperInterface::OPTION_WITH_COLUMNS]
      && !empty($options[EntityDataMapperInterface::OPTION_COLUMNS])
    ) {
      $columns = $options[EntityDataMapperInterface::OPTION_COLUMNS];
    } else {
      $columns = [];
    }

    $clientVersion = $this->clientService->getClientVersion($client);
    $availableVersions = $this->versionService->getVersionListSince($clientVersion);

    $availableColumns = [];

    foreach ($columns as $propertyKey => $column) {
      $property = $schema->getProperty($propertyKey);

      $propertyVersion = $property->getVersion();

      if($propertyVersion instanceof VersionInformation && $propertyVersion->isTypeSince()) {
        $propertyVersion = $propertyVersion->getVersion();
      }

      if(!$propertyVersion || in_array($propertyVersion, $availableVersions)) {
        $availableColumns[$propertyKey] = $column;
      }
    }

    return $availableColumns;
  }

}