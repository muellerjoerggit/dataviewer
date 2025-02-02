<?php

namespace App\DaViEntity\Schema;

use App\Database\Aggregation\AggregationConfigurationBuilder;
use App\Database\SqlFilter\SqlFilterDefinitionBuilder;
use App\Database\TableReferenceHandler\Attribute\TableReferenceAttrInterface;
use App\DaViEntity\EntityTypeAttributesReader;
use App\DaViEntity\Schema\Attribute\DatabaseAttr;
use App\DaViEntity\Schema\Attribute\EntityOverviewSchemaAttr as EntityOverviewClass;
use App\DaViEntity\Schema\Attribute\EntityTypeAttr;
use App\DaViEntity\Schema\Attribute\ExtendedEntityOverviewSchemaAttr as ExtendedEntityOverviewClass;
use App\DaViEntity\Schema\Attribute\LabelPropertySchemaAttr as LabelPropClass;
use App\Item\Property\Attribute\EntityOverviewPropertyAttr;
use App\Item\Property\Attribute\ExtendedEntityOverviewPropertyAttr;
use App\Item\Property\Attribute\LabelPropertyAttr as LabelPropProperty;
use App\Item\Property\Attribute\SearchPropertyAttr;
use App\Item\Property\Attribute\UniquePropertyAttr;
use App\Item\Property\PropertyAttributesReader;
use App\Item\Property\PropertyConfigurationBuilder;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Finder\SplFileInfo;

class EntitySchemaBuilder {

  private const string YAML_PARAM_FILTERS = 'filters';
  private const string YAML_PARAM_AGGREGATIONS = 'aggregations';
  private const string YAML_PARAM_PROPERTIES = 'properties';
  private const string YAML_PARAM_TYPE = 'entityType';
  private const string YAML_PARAM_LABEL = 'entityLabel';
  private const string YAML_PARAM_LABEL_PROPERTIES = 'labelProperties';
  private const string YAML_PARAM_SEARCH_PROPERTIES = 'searchProperties';
  private const string YAML_PARAM_UNIQUE_PROPERTIES = 'uniqueProperties';
  private const string YAML_PARAM_OVERVIEW = 'entityOverview';
  private const string YAML_PARAM_EXT_OVERVIEW = 'extendedEntityOverview';

  private const string YAML_PARAM_DATABASE_CONFIG = 'databaseConfig';
  private const string YAML_PARAM_DATABASE = 'database';
  private const string YAML_PARAM_BASE_TABLE = 'baseTable';
  private const string YAML_PARAM_TABLE_REFERENCES = 'tableReferences';

  public function __construct(
    private readonly PropertyConfigurationBuilder $propertyConfigurationBuilder,
    private readonly SqlFilterDefinitionBuilder $filterDefinitionsBuilder,
    private readonly AggregationConfigurationBuilder $aggregationConfigurationBuilder,
    private readonly EntityTypeAttributesReader $attributesReader,
    private readonly PropertyAttributesReader $propertyAttributesReader,
  ) {}

  public function buildSchema(SplFileInfo $file, string $entityClass): EntitySchemaInterface | null {
    $attributesContainer = $this->attributesReader->buildSchemaAttributesContainer($entityClass);
    $this->propertyAttributesReader->appendPropertyAttributesContainer($attributesContainer, $entityClass);
    $yaml = Yaml::parseFile($file->getRealPath());
    $schema = new EntitySchema($entityClass);
    $reflection = $this->reflect($entityClass);
    if(!$this->fillSchemaBasics($schema, $attributesContainer)) {
      return null;
    }
    $this->fillDatabase($schema, $attributesContainer);
    $this->fillDatabaseDetails($schema, $attributesContainer);
    $this->fillProperties($schema, $yaml);

    if(isset($yaml[self::YAML_PARAM_FILTERS])) {
      $this->buildFilters($schema,  $yaml);
    }

    if(isset($yaml[self::YAML_PARAM_AGGREGATIONS])) {
      $this->buildAggregations($schema,  $yaml);
    }

    $this->fillSpecialProperties($reflection, $schema);

    return $schema;
  }

  private function reflect(string $entityClass): ReflectionClass | null {
    try {
      return new ReflectionClass($entityClass);
    } catch (\ReflectionException $exception) {
      return null;
    }
  }

  private function fillSpecialProperties(ReflectionClass $reflection, EntitySchemaInterface $schema): bool {
    $uniqueProp = [];
    $labelTemp = [];
    $searchProps = [];
    $entityOverview = [];
    $extendedEntityOverview = [];
    foreach($reflection->getProperties() as $property) {
      $propertyName = $property->getName();
      $uniquePropertyAttr = $property->getAttributes(UniquePropertyAttr::class);
      $uniquePropertyAttr = reset($uniquePropertyAttr);
      if($uniquePropertyAttr instanceof ReflectionAttribute) {
        $name = $uniquePropertyAttr->newInstance()->getName();
        $uniqueProp[$name][] = $propertyName;
      }

      $this->processPropertyAttribute($property, LabelPropProperty::class, $labelTemp);
      $this->processPropertyAttribute($property, EntityOverviewPropertyAttr::class, $entityOverview);
      $this->processPropertyAttribute($property, ExtendedEntityOverviewPropertyAttr::class, $extendedEntityOverview);

      $searchPropertyAttr = $property->getAttributes(SearchPropertyAttr::class);
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

  private function fillProperties(EntitySchemaInterface $schema, array $yaml): void {
    if(!isset($yaml[self::YAML_PARAM_PROPERTIES])) {
      return;
    }

    foreach ($yaml[self::YAML_PARAM_PROPERTIES] as $name => $config) {
      $propertyConfiguration = $this->propertyConfigurationBuilder->buildPropertyConfiguration($config, $name, $schema);
      $schema->addProperty($propertyConfiguration);
    }
  }

  private function buildFilters(EntitySchema $schema, $yaml): void {
    foreach ($yaml[self::YAML_PARAM_FILTERS] as $key => $filterArray) {
      $filterArray['name'] = $key;
      $this->filterDefinitionsBuilder->buildFilterDefinition($schema, $filterArray);
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