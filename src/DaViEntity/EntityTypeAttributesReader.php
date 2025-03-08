<?php

namespace App\DaViEntity;

use App\Database\AggregationHandler\Attribute\AggregationDefinitionInterface;
use App\Database\BaseQuery\BaseQueryDefinition;
use App\Database\SqlFilterHandler\Attribute\SqlFilterDefinitionInterface;
use App\Database\TableReferenceHandler\Attribute\TableReferenceDefinitionInterface;
use App\DaViEntity\Schema\Attribute\DatabaseDefinition;
use App\DaViEntity\Schema\Attribute\EntityTypeDefinition;
use App\DaViEntity\Schema\SchemaDefinitionsContainer;
use App\EntityServices\AdditionalData\AdditionalDataProviderDefinition;
use App\EntityServices\AggregatedData\SqlAggregatedDataProviderDefinition;
use App\EntityServices\AvailabilityVerdict\AvailabilityVerdictDefinitionInterface;
use App\EntityServices\ColumnBuilder\ColumnBuilderDefinition;
use App\EntityServices\Creator\CreatorDefinition;
use App\EntityServices\DataProvider\DataProviderDefinition;
use App\EntityServices\EntityLabel\LabelCrafterDefinitionInterface;
use App\EntityServices\ListProvider\ListProviderDefinition;
use App\EntityServices\OverviewBuilder\OverviewBuilderDefinition;
use App\EntityServices\Refiner\RefinerDefinition;
use App\EntityServices\Repository\RepositoryDefinition;
use App\EntityServices\SimpleSearch\SimpleSearchDefinition;
use App\EntityServices\Validator\ValidatorDefinitionInterface;
use App\EntityServices\ViewBuilder\ViewBuilderDefinition;
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
    } elseif ($attribute instanceof AvailabilityVerdictDefinitionInterface && $attribute->isValid()) {
      $container->addAvailabilityVerdictDefinition($attribute);
    }

    switch(get_class($attribute)) {
      case EntityTypeDefinition::class:
        $container->setEntityTypeDefinition($attribute);
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
    return $this->getAttributeKey($classname, EntityTypeDefinition::class, EntityTypeDefinition::NAME_PROPERTY, NullEntity::ENTITY_TYPE);
  }

  private function resolveEntityClass(string | EntityInterface $classname): string {
    if($classname instanceof EntityInterface) {
      return get_class($classname);
    }

    return $classname;
  }

}