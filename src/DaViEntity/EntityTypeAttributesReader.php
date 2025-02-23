<?php

namespace App\DaViEntity;

use App\Database\AggregationHandler\Attribute\AggregationDefinitionInterface;
use App\Database\BaseQuery\BaseQueryDefinition;
use App\Database\SqlFilterHandler\Attribute\SqlFilterDefinitionInterface;
use App\Database\TableReferenceHandler\Attribute\TableReferenceDefinitionInterface;
use App\DaViEntity\AdditionalData\AdditionalDataProviderDefinition;
use App\DaViEntity\ColumnBuilder\ColumnBuilderDefinition;
use App\DaViEntity\Creator\CreatorDefinition;
use App\DaViEntity\DataProvider\DataProviderDefinition;
use App\DaViEntity\ListProvider\ListProviderDefinition;
use App\DaViEntity\OverviewBuilder\OverviewBuilderDefinition;
use App\DaViEntity\Refiner\RefinerDefinition;
use App\DaViEntity\Repository\RepositoryDefinition;
use App\DaViEntity\Schema\Attribute\DatabaseDefinition;
use App\DaViEntity\Schema\Attribute\EntityTypeAttr;
use App\DaViEntity\Schema\SchemaDefinitionsContainer;
use App\DaViEntity\SimpleSearch\SimpleSearchDefinition;
use App\DaViEntity\Validator\ValidatorDefinitionInterface;
use App\DaViEntity\ViewBuilder\ViewBuilderDefinition;
use App\EntityServices\AggregatedData\SqlAggregatedDataProviderDefinition;
use App\EntityServices\EntityLabel\LabelCrafterDefinitionInterface;
use App\EntityTypes\NullEntity\NullEntity;
use App\Services\AbstractAttributesReader;
use App\Services\EntityAction\EntityActionDefinitionInterface;
use ReflectionAttribute;

class EntityTypeAttributesReader extends AbstractAttributesReader {

  public function buildSchemaAttributesContainer(string $entityClass): SchemaDefinitionsContainer {
    $container = new SchemaDefinitionsContainer();
    $reflection = $this->reflectClass($entityClass);

    if(!$reflection) {
      return $container;
    }

    foreach($reflection->getAttributes() as $attribute) {
      $instance = $attribute->newInstance();
      $this->addAttributeInstanceToContainer($container, $instance);
    }

    return $container;
  }

  private function addAttributeInstanceToContainer(SchemaDefinitionsContainer $container, mixed $attribute): void {
    if(!is_object($attribute)) {
      return;
    }

    if($attribute instanceof TableReferenceDefinitionInterface) {
      $container->addTableReferenceAttribute($attribute);
      return;
    } elseif ($attribute instanceof EntityActionDefinitionInterface) {
      $container->addEntityActionConfigAttribute($attribute);
      return;
    } elseif ($attribute instanceof SqlFilterDefinitionInterface && $attribute->isValid()) {
      $container->addSqlFilterDefinitionsAttribute($attribute);
    } elseif ($attribute instanceof AggregationDefinitionInterface && $attribute->isValid()) {
      $container->addAggregationDefinitionAttribute($attribute);
    } elseif ($attribute instanceof LabelCrafterDefinitionInterface && $attribute->isValid()) {
      $container->addLabelCrafterDefinition($attribute);
    } elseif ($attribute instanceof ValidatorDefinitionInterface && $attribute->isValid()) {
      $container->addValidatorDefinition($attribute);
    }

    switch(get_class($attribute)) {
      case EntityTypeAttr::class:
        $container->setEntityTypeAttr($attribute);
        break;
      case DatabaseDefinition::class:
        $container->setDatabaseDefinition($attribute);
        break;
      case RepositoryDefinition::class:
        $container->addRepositoryDefinition($attribute);
        break;
      case BaseQueryDefinition::class:
        $container->addBaseQueryDefinition($attribute);
        break;
      case SimpleSearchDefinition::class:
        $container->addSimpleSearchDefinition($attribute);
        break;
      case DataProviderDefinition::class:
        $container->addDataProviderDefinition($attribute);
        break;
      case CreatorDefinition::class:
        $container->addCreatorDefinition($attribute);
        break;
      case RefinerDefinition::class:
        $container->addRefinerDefinition($attribute);
        break;
      case ColumnBuilderDefinition::class:
        $container->addColumnBuilderDefinition($attribute);
        break;
      case ListProviderDefinition::class:
        $container->addListProviderDefinition($attribute);
        break;
      case AdditionalDataProviderDefinition::class:
        $container->addAdditionalDataProviderDefinition($attribute);
        break;
      case ViewBuilderDefinition::class:
        $container->addViewBuilderDefinition($attribute);
        break;
      case OverviewBuilderDefinition::class:
        $container->addOverviewBuilderDefinition($attribute);
        break;
      case SqlAggregatedDataProviderDefinition::class:
        $container->addAggregatedDataProviderDefinition($attribute);
        break;
    }
  }

  private function getAttributeKey(string $classname, string $attributeClass, string $key, mixed $default): mixed {
    $reflection = $this->reflectClass($classname);

    if($reflection) {
      $attributes = $reflection->getAttributes($attributeClass);
      $attribute = reset($attributes);

      if(!$attribute instanceof ReflectionAttribute) {
        return $default;
      }

      $arguments = $attribute->getArguments();
      if(isset($arguments[$key])) {
        return $arguments[$key];
      }
    }

    return $default;
  }

  public function getEntityType(string | EntityInterface $classname): ?string {
    $classname = $this->resolveEntityClass($classname);
    return $this->getAttributeKey($classname, EntityTypeAttr::class, EntityTypeAttr::NAME_PROPERTY, NullEntity::ENTITY_TYPE);
  }

  private function resolveEntityClass(string | EntityInterface $classname): string {
    if($classname instanceof EntityInterface) {
      return get_class($classname);
    }

    return $classname;
  }

}