<?php

namespace App\Item\Property;

use App\Database\TableReference\TableReferenceHandlerLocator;
use App\DaViEntity\Schema\EntitySchema;
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

//    if(isset($config[VersionService::YAML_PARAM_VERSION])) {
//      $this->fillVersion($container[VersionService::YAML_PARAM_VERSION], $propertyConfiguration);
//    }
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
    } elseif($handlerDefinition instanceof AdditionalDataItemHandlerDefinitionInterface) {
      $propertyConfiguration->setAdditionalDataItemHandlerDefinition($handlerDefinition);
    } elseif($handlerDefinition instanceof ValidatorItemHandlerDefinitionInterface) {
      $propertyConfiguration->addValidatorItemHandlerDefinition($handlerDefinition);
    }
  }

  private function fillVersion(array $yaml, PropertyConfiguration $propertyConfiguration): void {
    if(isset($yaml[VersionService::YAML_PARAM_SINCE_VERSION])) {
      $version = new VersionInformation($yaml[VersionService::YAML_PARAM_SINCE_VERSION], VersionInformation::TYPE_SINCE_VERSION);
      $propertyConfiguration->setVersion($version);
    }
  }

}