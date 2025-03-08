<?php

namespace App\Item\Property;

use App\Database\TableReference\SimpleTableReferenceHandlerInterface;
use App\Database\TableReference\TableReferenceHandlerLocator;
use App\DaViEntity\Schema\EntitySchema;
use App\Item\ItemHandler_AdditionalData\Attribute\AdditionalDataHandlerDefinitionInterface;
use App\Item\ItemHandler_EntityReference\Attribute\EntityReferenceItemHandlerDefinitionInterface;
use App\Item\ItemHandler_Formatter\Attribute\FormatterItemHandlerDefinitionInterface;
use App\Item\ItemHandler_PreRendering\Attribute\PreRenderingItemHandlerDefinitionInterface;
use App\Item\ItemHandler_Validator\Attribute\ValidatorItemHandlerDefinitionInterface;
use App\Services\Version\VersionInformation;
use App\Services\Version\VersionService;

class PropertyConfigurationBuilder {

  public function __construct(
    private readonly TableReferenceHandlerLocator $tableReferenceHandlersLocator,
    private readonly VersionService $versionService,
  ) {}

  public function buildBasicPropertyConfiguration(PropertyAttributesContainer $container, EntitySchema $schema): PropertyConfiguration {
    $propertyConfiguration = new PropertyConfiguration($container->getPropertyName());

    $this->fillBasic($container, $propertyConfiguration);
    $this->fillDatabaseColumn($container, $propertyConfiguration, $schema);

    return $propertyConfiguration;
  }

  public function fillPropertyConfiguration(PropertyAttributesContainer $container, EntitySchema $schema): void {
    if(!$container->hasPropertyConfiguration()) {
      return;
    }

    $propertyConfiguration = $container->getPropertyConfiguration();

    $this->fillTableReference($container, $propertyConfiguration, $schema);
    $this->fillHandler($container, $propertyConfiguration);
  }

  private function fillBasic(PropertyAttributesContainer $container, PropertyConfiguration $propertyConfiguration): void {
    $propertyDefinition = $container->getPropertyDefinition();

    $propertyConfiguration
      ->setCardinality($propertyDefinition->getCardinality())
      ->setDataType($propertyDefinition->getDataType())
      ->setLabel($propertyDefinition->getLabel());

    if($propertyDefinition->hasDescription()) {
      $propertyConfiguration->setDescription($propertyDefinition->getDescription());
    }

    if($container->hasPropertySetting()) {
      foreach($container->iteratePropertySetting() as $propertySetting) {
        $propertyConfiguration->addSetting($propertySetting);
      }
    }

    $versionList = $this->versionService->getVersionList($propertyDefinition);
    $propertyConfiguration->setVersionList($versionList);
  }

  private function fillDatabaseColumn(PropertyAttributesContainer $container, PropertyConfiguration $propertyConfiguration, EntitySchema $schema): void {
    if(!$container->hasDatabasePropertyDefinition()) {
      return;
    }

    $databasePropertyDefinition = $container->getDatabasePropertyDefinition();

    if (!$databasePropertyDefinition->hasColumn()) {
      return;
    }

    $column = $schema->getBaseTable() . '.' . $databasePropertyDefinition->getColumn();
    $propertyConfiguration->setColumn($column);
    $schema->addColumn($propertyConfiguration);
  }

  private function fillTableReference(PropertyAttributesContainer $container, PropertyConfiguration $propertyConfiguration, EntitySchema $schema): void {
    if(!$container->hasTableReferencePropertyDefinition()) {
      return;
    }

    $tableReferencePropertyDefinition = $container->getTableReferencePropertyDefinition();
    $name = $tableReferencePropertyDefinition->getTableReferenceName();

    if(!$tableReferencePropertyDefinition->isValid() || !$schema->hasTableReference($name)) {
      return;
    }

    $tableReferenceConfiguration = $schema->getTableReference($name);
    $handler = $this->tableReferenceHandlersLocator->getTableHandlerFromConfiguration($tableReferenceConfiguration);
    if(!$handler instanceof SimpleTableReferenceHandlerInterface) {
      return;
    }

    $toSchema = $handler->getToSchema($tableReferenceConfiguration);
    $property = $tableReferencePropertyDefinition->getProperty();

    if(!$toSchema->hasProperty($property)) {
      return;
    }

    $referencePropertyConfig = $toSchema->getProperty($property);
    $propertyConfiguration->setTableReference($tableReferenceConfiguration);
    $schema->addTableReferenceColumn($tableReferenceConfiguration, $referencePropertyConfig, $propertyConfiguration->getItemName());
}

  private function fillHandler(PropertyAttributesContainer $container, PropertyConfiguration $propertyConfiguration): void {
    foreach ($container->iterateItemHandlerDefinitions() as $handlerDefinition) {
      $this->processHandler($handlerDefinition, $propertyConfiguration);
    }
  }

  private function processHandler($handlerDefinition, PropertyConfiguration $propertyConfiguration): void {
    if($handlerDefinition instanceof PreRenderingItemHandlerDefinitionInterface) {
      $propertyConfiguration->setPreRenderingItemHandlerDefinition($handlerDefinition);
    } elseif($handlerDefinition instanceof FormatterItemHandlerDefinitionInterface) {
      $propertyConfiguration->setFormatterItemHandlerDefinition($handlerDefinition);
    } elseif($handlerDefinition instanceof EntityReferenceItemHandlerDefinitionInterface) {
      $propertyConfiguration->setReferenceItemHandlerDefinition($handlerDefinition);
    } elseif($handlerDefinition instanceof AdditionalDataHandlerDefinitionInterface) {
      $propertyConfiguration->setAdditionalDataHandlerDefinition($handlerDefinition);
    } elseif($handlerDefinition instanceof ValidatorItemHandlerDefinitionInterface) {
      $propertyConfiguration->addValidatorItemHandlerDefinition($handlerDefinition);
    }
  }

}