<?php

namespace App\Item\Property;

use App\Database\SqlFilterHandler\Attribute\SqlFilterDefinitionInterface;
use App\Database\TableReference\TableReferencePropertyDefinition;
use App\DaViEntity\Schema\SchemaDefinitionsContainer;
use App\Item\ItemHandler_AdditionalData\Attribute\AdditionalDataItemHandlerDefinitionInterface;
use App\Item\ItemHandler_EntityReference\Attribute\EntityReferenceItemHandlerDefinitionInterface;
use App\Item\ItemHandler_Formatter\Attribute\FormatterItemHandlerDefinitionInterface;
use App\Item\ItemHandler_PreRendering\Attribute\PreRenderingItemHandlerDefinitionInterface;
use App\Item\ItemHandler_Validator\Attribute\ValidatorItemHandlerDefinitionInterface;
use App\Item\Property\Attribute\DatabaseColumnDefinition;
use App\Item\Property\Attribute\EntityOverviewPropertyAttr;
use App\Item\Property\Attribute\ExtendedEntityOverviewPropertyAttr;
use App\Item\Property\Attribute\LabelPropertyAttr;
use App\Item\Property\Attribute\PropertyAttr;
use App\Item\Property\Attribute\PropertyPreDefinedAttr;
use App\Item\Property\Attribute\ItemSettingInterface;
use App\Item\Property\Attribute\SearchPropertyDefinition;
use App\Item\Property\Attribute\UniquePropertyDefinition;
use App\Services\AbstractAttributesReader;
use ReflectionAttribute;
use ReflectionProperty;

class PropertyAttributesReader extends AbstractAttributesReader {

  public function appendPropertyAttributesContainer(SchemaDefinitionsContainer $container, string $entityClass): bool {
    $reflection = $this->reflectClass($entityClass);

    if(!$reflection) {
      return false;
    }

    foreach ($reflection->getProperties() as $property) {
      if(in_array($property->getName(), ['missingEntity', 'logIndex', 'logItems', 'schema', 'client'])) {
        continue;
      }
      $this->processProperty($property, $container);
    }

    return true;
  }

  private function processProperty(ReflectionProperty $property, SchemaDefinitionsContainer $schemaContainer): void {
    $propertyContainer = new PropertyAttributesContainer($property);

    foreach ($property->getAttributes() as $attribute) {
      $this->processReflectionAttribute($propertyContainer, $property, $attribute, $schemaContainer);
      $schemaContainer->addPropertyContainer($propertyContainer, $property->getName());
    }
  }

  private function processReflectionAttribute(
    PropertyAttributesContainer $propertyContainer,
    ReflectionProperty $property,
    ReflectionAttribute $attribute,
    SchemaDefinitionsContainer $schemaContainer
  ): void {
    $instance = $attribute->newInstance();
    $name = $property->getName();

    $this->processAttribute($propertyContainer, $schemaContainer, $name, $instance);
  }

  private function processAttribute(
    PropertyAttributesContainer $propertyContainer,
    SchemaDefinitionsContainer $schemaContainer,
    string $name,
    $instance
  ): void {
    if($instance instanceof PropertyPreDefinedAttr) {
      foreach ($instance->iteratePreDefinedAttributes() as $attribute) {
        $this->processAttribute($propertyContainer, $schemaContainer, $name, $attribute);
      }
    } elseif($instance instanceof PropertyAttr) {
      $instance->setProperty($name);
      $propertyContainer->setPropertyAttr($instance);
    } elseif ($instance instanceof LabelPropertyAttr) {
      $instance->setProperty($name);
      $schemaContainer->addLabelDefinition($instance);
    } elseif ($instance instanceof SearchPropertyDefinition) {
      $instance->setProperty($name);
      $schemaContainer->addSearchPropertyDefinition($instance);
    } elseif ($instance instanceof UniquePropertyDefinition) {
      $instance->setProperty($name);
      $schemaContainer->addUniquePropertyDefinition($instance);
    } elseif ($instance instanceof EntityOverviewPropertyAttr) {
      $instance->setProperty($name);
      $schemaContainer->addEntityOverviewDefinition($instance);
    } elseif ($instance instanceof ExtendedEntityOverviewPropertyAttr) {
      $instance->setProperty($name);
      $schemaContainer->addExtendedEntityOverviewDefinition($instance);
    } elseif ($instance instanceof ValidatorItemHandlerDefinitionInterface) {
      $propertyContainer->addValidatorItemHandler($instance);
    } elseif ($instance instanceof PreRenderingItemHandlerDefinitionInterface) {
      $propertyContainer->setPreRenderingItemHandlerDefinition($instance);
    } elseif ($instance instanceof FormatterItemHandlerDefinitionInterface) {
      $propertyContainer->setFormatterItemHandlerDefinition($instance);
    } elseif ($instance instanceof EntityReferenceItemHandlerDefinitionInterface) {
      $propertyContainer->setReferenceItemHandlerDefinition($instance);
    } elseif ($instance instanceof AdditionalDataItemHandlerDefinitionInterface) {
      $propertyContainer->setAdditionalDataItemHandlerDefinition($instance);
    } elseif ($instance instanceof SqlFilterDefinitionInterface) {
      $instance->setProperty($name);
      $schemaContainer->addSqlFilterDefinitionsAttribute($instance);
    } elseif ($instance instanceof DatabaseColumnDefinition) {
      if(!$instance->hasColumn()) {
        $instance->setColumn($name);
      }
      $propertyContainer->setDatabasePropertyDefinition($instance);
    } elseif ($instance instanceof ItemSettingInterface) {
      $propertyContainer->addPropertySetting($instance);
    } elseif ($instance instanceof TableReferencePropertyDefinition) {
      $propertyContainer->setTableReferencePropertyDefinition($instance);
    }
  }

}