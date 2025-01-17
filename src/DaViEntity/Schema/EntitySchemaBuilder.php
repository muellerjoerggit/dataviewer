<?php

namespace App\DaViEntity\Schema;

use App\Database\Aggregation\AggregationConfigurationBuilder;
use App\Database\SqlFilter\SqlFilterDefinitionBuilder;
use App\Database\TableReference\TableReferenceConfigurationBuilder;
use App\DaViEntity\Schema\Attribute\DatabaseAttr;
use App\DaViEntity\Schema\Attribute\EntityOverviewSchemaAttr as EntityOverviewClass;
use App\DaViEntity\Schema\Attribute\ExtendedEntityOverviewSchemaAttr as ExtendedEntityOverviewClass;
use App\DaViEntity\Schema\Attribute\LabelPropertySchemaAttr as LabelPropClass;
use App\Item\Property\Attribute\EntityOverviewPropertyAttr;
use App\Item\Property\Attribute\ExtendedEntityOverviewPropertyAttr;
use App\Item\Property\Attribute\LabelPropertyAttr as LabelPropProperty;
use App\Item\Property\Attribute\SearchPropertyAttr;
use App\Item\Property\Attribute\UniquePropertyAttr;
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
    private readonly TableReferenceConfigurationBuilder $tableReferenceConfigurationBuilder,
  ) {}

  public function buildSchema(SplFileInfo $file, string $entityClass): EntitySchemaInterface {
    $yaml = Yaml::parseFile($file->getRealPath());
    $schema = new EntitySchema();
    $reflection = $this->reflect($entityClass);
    $this->fillSchemaBasics($schema, $yaml);
    $this->fillDatabase($reflection, $schema);
    $this->fillDatabaseDetails($schema, $yaml);
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

  private function fillSchemaBasics(EntitySchemaInterface $schema, array $yaml): void {
    $schema
      ->setEntityType($yaml[self::YAML_PARAM_TYPE])
      ->setEntityLabel($yaml[self::YAML_PARAM_LABEL])
    ;
  }

  private function fillDatabaseDetails(EntitySchemaInterface $schema, array $yaml): void {
    if(!isset($yaml[self::YAML_PARAM_DATABASE_CONFIG])) {
      return;
    }

    $yaml = $yaml[self::YAML_PARAM_DATABASE_CONFIG];

    if(isset($yaml[self::YAML_PARAM_TABLE_REFERENCES])) {
      $this->tableReferenceConfigurationBuilder->processYaml($yaml[self::YAML_PARAM_TABLE_REFERENCES], $schema);
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

  private function fillDatabase(ReflectionClass $reflection, EntitySchemaInterface $schema): void {
    $attr = $reflection->getAttributes(DatabaseAttr::class);
    $attr = reset($attr);

    if(!$attr instanceof ReflectionAttribute) {
      return;
    }

    $attr = $attr->newInstance();

    if(!$attr instanceof DatabaseAttr || !$attr->isValid()) {
      return;
    }

    $schema->setDatabase($attr->databaseClass);
    $schema->setBaseTable($attr->baseTable);
  }

}