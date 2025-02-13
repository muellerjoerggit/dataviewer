<?php

namespace App\Item\Property;

use App\Database\SqlFilter\FilterGroup;
use App\Database\SqlFilter\SqlFilterDefinition;
use App\Database\SqlFilter\SqlFilterDefinitionBuilder;
use App\Database\SqlFilterHandler\Attribute\SqlFilterDefinitionInterface;
use App\Database\TableReference\TableReferenceHandlerLocator;
use App\DaViEntity\Schema\EntitySchema;
use App\Item\ItemConfigurationInterface;
use App\Item\ItemHandler_AdditionalData\Attribute\AdditionalDataItemHandlerDefinitionInterface;
use App\Item\ItemHandler_EntityReference\Attribute\EntityReferenceItemHandlerDefinitionInterface;
use App\Item\ItemHandler_Formatter\Attribute\FormatterItemHandlerDefinitionInterface;
use App\Item\ItemHandler_PreRendering\Attribute\PreRenderingItemHandlerDefinitionInterface;
use App\Item\ItemHandler_Validator\Attribute\ValidatorItemHandlerDefinitionInterface;
use App\Services\Version\VersionInformation;
use App\Services\Version\VersionService;

class PropertyConfigurationBuilder {

  public function __construct(
    private readonly TableReferenceHandlerLocator $tableReferenceHandlersLocator,
  ) {}

  public function buildPropertyConfiguration(PropertyAttributesContainer $container, EntitySchema $schema): PropertyConfiguration {
    $propertyName = $container->getPropertyName();
    $propertyConfiguration = $this->createPropertyConfiguration($propertyName);

    return $this->fillPropertyConfiguration($container, $propertyConfiguration, $schema);
  }

  private function createPropertyConfiguration(string $propertyName): ?PropertyConfiguration {
    return new PropertyConfiguration($propertyName);
  }

  private function fillPropertyConfiguration(PropertyAttributesContainer $container, PropertyConfiguration $propertyConfiguration, EntitySchema $schema): PropertyConfiguration {
    $this->fillBasic($container, $propertyConfiguration);
    $this->fillDatabase($container, $propertyConfiguration, $schema);
    $this->fillHandler($container, $propertyConfiguration);


//    if(isset($config[VersionService::YAML_PARAM_VERSION])) {
//      $this->fillVersion($container[VersionService::YAML_PARAM_VERSION], $propertyConfiguration);
//    }

    return $propertyConfiguration;
  }

  private function fillBasic(PropertyAttributesContainer $container, PropertyConfiguration $propertyConfiguration): void {
    $propertyAttr = $container->getPropertyAttr();

    $propertyConfiguration
      ->setCardinality($propertyAttr->getCardinality())
      ->setDataType($propertyAttr->getDataType())
      ->setLabel($propertyAttr->getLabel());

    if($propertyAttr->hasDescription()) {
      $propertyConfiguration->setDescription($propertyAttr->getDescription());
    }

    if($container->hasPropertySetting()) {
      foreach($container->iteratePropertySetting() as $propertySetting) {
        $propertyConfiguration->addSetting($propertySetting);
      }
    }
  }

  private function fillDatabase(PropertyAttributesContainer $container, PropertyConfiguration $propertyConfiguration, EntitySchema $schema): void {
    if(!$container->hasDatabaseAttr()) {
      return;
    }

    $databaseAttr = $container->getDatabaseAttr();

    if($databaseAttr->hasTableReferenceName() && $databaseAttr->hasColumn()) {
      $tableReferenceConfiguration = $schema->getTableReference($databaseAttr->getTableReferenceName());
      $handler = $this->tableReferenceHandlersLocator->getTableHandlerFromConfiguration($tableReferenceConfiguration);
      $baseTable = $handler->getReferencedTableName($tableReferenceConfiguration);
      $propertyConfiguration->setTableReference($tableReferenceConfiguration);
      $column = $baseTable . '.' . $databaseAttr->getColumn();
      $schema->addTableReferenceColumn($tableReferenceConfiguration, $column, $propertyConfiguration->getItemName());
    } elseif ($databaseAttr->hasColumn()) {
      $column = $databaseAttr->getColumn();
    } else {
      return;
    }

    $propertyConfiguration->setColumn($column);
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
    } elseif($handlerDefinition instanceof AdditionalDataItemHandlerDefinitionInterface) {
      $propertyConfiguration->setAdditionalDataItemHandlerDefinition($handlerDefinition);
    } elseif($handlerDefinition instanceof ValidatorItemHandlerDefinitionInterface) {
      $propertyConfiguration->addValidatorItemHandler($handlerDefinition);
    }
  }

  private function fillVersion(array $yaml, PropertyConfiguration $propertyConfiguration): void {
    if(isset($yaml[VersionService::YAML_PARAM_SINCE_VERSION])) {
      $version = new VersionInformation($yaml[VersionService::YAML_PARAM_SINCE_VERSION], VersionInformation::TYPE_SINCE_VERSION);
      $propertyConfiguration->setVersion($version);
    }
  }

}