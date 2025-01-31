<?php

namespace App\DaViEntity\Schema;

use App\DaViEntity\EntityInterface;
use App\Services\ClientService;
use App\Services\Version\VersionInformation;
use App\Services\Version\VersionService;

class VersionProperties {

  public function __construct(
    private readonly EntityTypeSchemaRegister $entityTypeSchemaRegister,
    private readonly VersionService $versionService,
    private readonly ClientService $clientService,
  ) {}

  public function filterPropertyKeysByVersion(string | EntityInterface $entityClass, array $input, string $client): array {
    $clientVersion = $this->clientService->getClientVersion($client);
    $availableVersions = $this->versionService->getVersionListSince($clientVersion);
    $schema = $this->entityTypeSchemaRegister->getSchemaFromEntityClass($entityClass);

    $ret = [];
    foreach ($input as $propertyKey => $data) {
      if(!$schema->hasProperty($propertyKey)) {
        continue;
      }

      $property = $schema->getProperty($propertyKey);

      $propertyVersion = $property->getVersion();

      if($propertyVersion instanceof VersionInformation && $propertyVersion->isTypeSince()) {
        $propertyVersion = $propertyVersion->getVersion();
      }

      if(!$propertyVersion || in_array($propertyVersion, $availableVersions)) {
        $ret[$propertyKey] = $data;
      }
    }

    return $ret;
  }

}