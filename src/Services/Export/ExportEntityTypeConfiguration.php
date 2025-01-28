<?php

namespace App\Services\Export;

use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\DaViEntity\Schema\EntityTypesRegister;
use App\Item\ItemConfigurationInterface;
use App\Item\ItemHandler_EntityReference\EntityReferenceItemHandlerLocator;

class ExportEntityTypeConfiguration {

  public function __construct(
    private readonly EntityTypeSchemaRegister $schemaRegister,
    private readonly EntityTypesRegister $typesRegister,
    private readonly EntityReferenceItemHandlerLocator $referenceItemHandlerLocator,
  ) {}

  public function getEntityTypeConfiguration(string $entityType): array {
    $entityClass = $this->typesRegister->getEntityClassByEntityType($entityType);
    $schema = $this->schemaRegister->getSchemaFromEntityClass($entityClass);
    [$properties, $references] = $this->getProperties($schema);
    return [
      'entityType' => $entityType,
      'entityLabel' => $schema->getEntityLabel(),
      'properties' => $properties,
      'references' => $references,
    ];
  }

  private function getEntityReference(ItemConfigurationInterface $itemConfiguration): array {
    if(!$itemConfiguration->hasEntityReferenceHandler()) {
      return [];
    }

    $handler = $this->referenceItemHandlerLocator->getEntityReferenceHandlerFromItem($itemConfiguration);
    $referenceEntityType = $handler->getTargetEntityType($itemConfiguration);

      $schema = $this->schemaRegister->getEntityTypeSchema($referenceEntityType);
      return [
        'entityType' => $referenceEntityType,
        'entityLabel' => $schema->getEntityLabel(),
        'property' => $itemConfiguration->getItemName()
      ];
  }

  private function getProperties(EntitySchema $schema): array {
    $properties = [];
    $references = [];
    foreach ($schema->iterateProperties() as $name => $config) {
      $properties[$name] = [
        'key' => $name,
        'label' => $config->getLabel(),
        'description' => $config->getDescription(),
        'cardinality' => $config->getCardinality(),
      ];
      $reference = $this->getEntityReference($config);
      if(!empty($reference)) {
        $references[$name] = $reference;
      }

    }

    return [$properties, $references];
  }

}