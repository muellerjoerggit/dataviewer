<?php

namespace App\DaViEntity\Schema;

use App\Database\Aggregation\AggregationConfigurationBuilder;
use App\Database\TableReferenceHandler\Attribute\TableReferenceAttrInterface;
use App\DaViEntity\EntityTypeAttributesReader;
use App\DaViEntity\Schema\Attribute\DatabaseAttr;
use App\DaViEntity\Schema\Attribute\EntityOverviewDefinitionSchemaAttr as EntityOverviewClass;
use App\DaViEntity\Schema\Attribute\EntityTypeAttr;
use App\DaViEntity\Schema\Attribute\ExtendedEntityOverviewDefinitionSchemaAttr as ExtendedEntityOverviewClass;
use App\DaViEntity\Schema\Attribute\LabelDefinitionSchemaAttr as LabelPropClass;
use App\Item\Property\Attribute\EntityOverviewPropertyAttr;
use App\Item\Property\Attribute\ExtendedEntityOverviewPropertyAttr;
use App\Item\Property\Attribute\LabelPropertyAttr as LabelPropProperty;
use App\Item\Property\Attribute\SearchPropertyDefinition;
use App\Item\Property\Attribute\UniquePropertyDefinition;
use App\Item\Property\PropertyAttributesReader;
use App\Item\Property\PropertyConfigurationBuilder;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Finder\SplFileInfo;

class EntitySchemaBuilder {

  private const string YAML_PARAM_AGGREGATIONS = 'aggregations';
  private const string YAML_PARAM_PROPERTIES = 'properties';

  public function __construct(
    private readonly PropertyConfigurationBuilder $propertyConfigurationBuilder,
    private readonly AggregationConfigurationBuilder $aggregationConfigurationBuilder,
    private readonly EntityTypeAttributesReader $attributesReader,
    private readonly PropertyAttributesReader $propertyAttributesReader,
  ) {}

  public function buildSchema(SplFileInfo $file, string $entityClass): EntitySchemaInterface | null {
    $attributesContainer = $this->attributesReader->buildSchemaAttributesContainer($entityClass);
    $this->propertyAttributesReader->appendPropertyAttributesContainer($attributesContainer, $entityClass);
    $yaml = Yaml::parseFile($file->getRealPath());
    $schema = new EntitySchema($entityClass);

    if(!$attributesContainer->isValid()) {
      return null;
    }

    if(!$this->fillSchemaBasics($schema, $attributesContainer)) {
      return null;
    }
    $this->fillDatabase($schema, $attributesContainer);
    $this->fillDatabaseDetails($schema, $attributesContainer);
    $this->fillProperties($schema, $attributesContainer);

    if($attributesContainer->hasSqlFilterDefinitions()) {
      $this->buildFilters($schema, $attributesContainer);
    }

    if(isset($yaml[self::YAML_PARAM_AGGREGATIONS])) {
      $this->buildAggregations($schema,  $yaml);
    }

    $this->fillUniqueProperties($schema, $attributesContainer);
    $this->fillLabelProperties($schema, $attributesContainer);
    $this->fillSearchProperties($schema, $attributesContainer);
    $this->fillEntityOverview($schema, $attributesContainer);
    $this->fillExtendedEntityOverview($schema, $attributesContainer);

    if($attributesContainer->hasEntityActions()){
      $this->fillEntityActions($schema, $attributesContainer);
    }

    return $schema;
  }

  private function reflect(string $entityClass): ReflectionClass | null {
    try {
      return new ReflectionClass($entityClass);
    } catch (\ReflectionException $exception) {
      return null;
    }
  }

  private function fillEntityActions(EntitySchemaInterface $schema, SchemaAttributesContainer $container): void {
    foreach($container->iterateEntityActionConfigAttributes() as $attribute) {
      if(!$attribute->isValid()) {
        continue;
      }

      $schema->addEntityAction($attribute);
    }
  }

  private function fillUniqueProperties(EntitySchemaInterface $schema, SchemaAttributesContainer $container): void {
    $uniqueProperties = [];
    foreach ($container->iterateUniquePropertyDefinitions() as $definition) {
      $uniqueProperties[$definition->getName()] = $definition->getProperty();
    }

    $schema->setUniqueProperties($uniqueProperties);
  }

  private function fillLabelProperties(EntitySchemaInterface $schema, SchemaAttributesContainer $container): void {
    $labels = [];
    foreach ($container->iterateLabelDefinitions() as $labelAttribute) {
      $labels[] = [
        'name' => $labelAttribute->getPath(),
        'label' => $labelAttribute->getLabel(),
        'rank' => $labelAttribute->getRank(),
      ];
    }

    $labelProp = array_keys($this->sortProperties($labels));

    $schema->setEntityLabelProperties($labelProp);
  }

  private function fillEntityOverview(EntitySchemaInterface $schema, SchemaAttributesContainer $container): void {
    $entityOverview = [];
    foreach ($container->iterateEntityOverviewDefinitions() as $definition) {
      $entityOverview[] = [
        'name' => $definition->getPath(),
        'label' => $definition->getLabel(),
        'rank' => $definition->getRank(),
      ];
    }

    $entityOverview = $this->sortProperties($entityOverview);

    $schema->setEntityOverviewProperties($entityOverview);
  }

  private function fillExtendedEntityOverview(EntitySchemaInterface $schema, SchemaAttributesContainer $container): void {
    $extendedEntityOverview = [];
    foreach ($container->iterateExtendedEntityOverviewDefinitions() as $definition) {
      $extendedEntityOverview[] = [
        'name' => $definition->getPath(),
        'label' => $definition->getLabel(),
        'rank' => $definition->getRank(),
      ];
    }

    $extendedEntityOverview = $this->sortProperties($extendedEntityOverview);

    $schema->setExtendedEntityOverviewProperties($extendedEntityOverview);
  }

  private function fillSearchProperties(EntitySchemaInterface $schema, SchemaAttributesContainer $container): void {
    $searchProperties = [];
    foreach ($container->iterateSearchPropertyDefinitions() as $definition) {
      $searchProperties[] = $definition->getProperty();
    }

    $schema->setSearchProperties($searchProperties);
  }

  private function fillSpecialProperties(ReflectionClass $reflection, EntitySchemaInterface $schema): bool {
    $uniqueProp = [];
    $labelTemp = [];
    $searchProps = [];
    $entityOverview = [];
    $extendedEntityOverview = [];
    foreach($reflection->getProperties() as $property) {
      $propertyName = $property->getName();
      $uniquePropertyAttr = $property->getAttributes(UniquePropertyDefinition::class);
      $uniquePropertyAttr = reset($uniquePropertyAttr);
      if($uniquePropertyAttr instanceof ReflectionAttribute) {
        $name = $uniquePropertyAttr->newInstance()->getName();
        $uniqueProp[$name][] = $propertyName;
      }

      $this->processPropertyAttribute($property, LabelPropProperty::class, $labelTemp);
      $this->processPropertyAttribute($property, EntityOverviewPropertyAttr::class, $entityOverview);
      $this->processPropertyAttribute($property, ExtendedEntityOverviewPropertyAttr::class, $extendedEntityOverview);

      $searchPropertyAttr = $property->getAttributes(SearchPropertyDefinition::class);
      $searchPropertyAttr = reset($searchPropertyAttr);
      if($searchPropertyAttr instanceof ReflectionAttribute) {
        $searchProps[] = $propertyName;
      }
    }

    $this->processClassAttribute($reflection, LabelPropClass::class, $labelTemp);
    $this->processClassAttribute($reflection, EntityOverviewClass::class, $entityOverview);
    $this->processClassAttribute($reflection, ExtendedEntityOverviewClass::class, $extendedEntityOverview);


    if(empty($uniqueProp)) {
      return false;
    }

    $labelProp = [];
    if(!empty($labelTemp)) {
      $labelProp = $this->sortProperties($labelTemp);
    } else {
      $firstUniqueProp = reset($uniqueProp);
      foreach ($firstUniqueProp as $value) {
        $labelProp[$value] = '';
      }
    }

    $entityOverview = $this->sortProperties($entityOverview);
    $extendedEntityOverview = $this->sortProperties($extendedEntityOverview);
    $labelProp = array_keys($labelProp);

    $schema->setUniqueProperties($uniqueProp);
    $schema->setEntityLabelProperties($labelProp);
    $schema->setSearchProperties(empty($searchProps) ? $labelProp : $searchProps);
    $schema->setExtendedEntityOverviewProperties($extendedEntityOverview);
    $schema->setEntityOverviewProperties($entityOverview);
    return true;
  }

  private function processPropertyAttribute(ReflectionProperty $property, string $attrClass, array &$result): void {
    $propertyAttr = $property->getAttributes($attrClass);
    $propertyAttr = reset($propertyAttr);
    if($propertyAttr instanceof ReflectionAttribute) {
      $labelArgs = $propertyAttr->getArguments();

      $result[] = [
        'name' => $property->getName(),
        'label' => $labelArgs['label'] ?? '',
        'rank' => $labelArgs['rank'] ?? 0,
      ];
    }
  }

  private function processClassAttribute(ReflectionClass $reflection, string $attrClass, array &$result): void {
    $classAttributes = $reflection->getAttributes($attrClass);

    foreach ($classAttributes as $attribute) {
      $args = $attribute->getArguments();
      $path = $args['path'] ?? '';

      if(empty($path)) {
        continue;
      }

      $result[] = [
        'name' => $path,
        'label' => $args['label'] ?? '',
        'rank' => $args['rank'] ?? 0,
      ];
    }
  }

  private function sortProperties(array $properties): array {
    $ret = [];
    usort($properties, function($a, $b) {
      return $a['rank'] < $b['rank'] ? -1 : 1;
    });
    foreach($properties as $property) {
      $ret[$property['name']] = $property['label'];
    }

    return $ret;
  }

  private function fillSchemaBasics(EntitySchemaInterface $schema, SchemaAttributesContainer $container): bool {
    $attr = $container->getEntityTypeAttr();

    if(!$attr instanceof EntityTypeAttr) {
      return false;
    }

    $schema
      ->setEntityType($attr->getName())
      ->setEntityLabel($attr->getLabel())
    ;

    return true;
  }

  private function fillDatabaseDetails(EntitySchemaInterface $schema, SchemaAttributesContainer $container): void {
    foreach ($container->iterateTableReferenceAttributes() as $attribute) {
      if(!$attribute instanceof TableReferenceAttrInterface) {
        continue;
      }
      $attribute
        ->setExternalName($schema->getEntityType() . '_' . $attribute->getName())
        ->setFromEntityClass($schema->getEntityClass());

      $schema->addTableReference($attribute);
    }
  }

  private function fillProperties(EntitySchemaInterface $schema, SchemaAttributesContainer $container): void {
    foreach ($container->iteratePropertyContainer() as $container) {
      if(!$container->isValid()) {
        continue;
      }
      $propertyConfiguration = $this->propertyConfigurationBuilder->buildPropertyConfiguration($container, $schema);
      $schema->addProperty($propertyConfiguration);
    }
  }

  private function buildFilters(EntitySchemaInterface $schema, SchemaAttributesContainer $container): void {
    foreach ($container->iterateSqlFilterDefinitionAttributes() as $attribute) {
      if(!$attribute->isValid()) {
        continue;
      }

      $schema->addFilter($attribute);
    }
  }

  private function buildAggregations(EntitySchema $schema, $yaml): void {
    foreach($yaml[self::YAML_PARAM_AGGREGATIONS] as $key => $aggregationArray) {
      $aggregationConfiguration = $this->aggregationConfigurationBuilder->buildAggregationConfiguration($aggregationArray, $key);
      $schema->addAggregation($aggregationConfiguration);
    }
  }

  private function fillDatabase(EntitySchemaInterface $schema, SchemaAttributesContainer $container): void {
    $attr = $container->getDatabaseAttr();

    if(!$attr instanceof DatabaseAttr || !$attr->isValid()) {
      return;
    }

    $schema->setDatabase($attr->getDatabaseClass());
    $schema->setBaseTable($attr->getBaseTable());
  }

}