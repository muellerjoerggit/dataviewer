<?php

namespace App\DaViEntity\Schema;

use App\Database\Aggregation\AggregationConfigurationBuilder;
use App\Database\DaViDatabaseOne;
use App\Database\DaViDatabaseTwo;
use App\Database\SqlFilter\SqlFilterDefinitionBuilder;
use App\Database\TableReference\TableReferenceConfigurationBuilder;
use App\Item\Property\PropertyConfigurationBuilder;
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

  public function buildSchema(SplFileInfo $file): EntitySchemaInterface {
    $yaml = Yaml::parseFile($file->getRealPath());
    $schema = new EntitySchema();
    $this->fillSchemaBasics($schema, $yaml);
    $this->fillDatabaseDetails($schema, $yaml);
    $this->fillProperties($schema, $yaml);

    if(isset($yaml[self::YAML_PARAM_FILTERS])) {
      $this->buildFilters($schema,  $yaml);
    }

    if(isset($yaml[self::YAML_PARAM_AGGREGATIONS])) {
      $this->buildAggregations($schema,  $yaml);
    }

    return $schema;
  }

  private function fillSchemaBasics(EntitySchemaInterface $schema, array $yaml): void {
    $schema
      ->setEntityType($yaml[self::YAML_PARAM_TYPE])
      ->setEntityLabel($yaml[self::YAML_PARAM_LABEL])
      ->setUniqueProperties($yaml[self::YAML_PARAM_UNIQUE_PROPERTIES])
      ->setEntityLabelProperties($yaml[self::YAML_PARAM_LABEL_PROPERTIES] ?? $yaml[self::YAML_PARAM_UNIQUE_PROPERTIES])
    ;

    if(isset($yaml[self::YAML_PARAM_SEARCH_PROPERTIES]) && is_array($yaml[self::YAML_PARAM_SEARCH_PROPERTIES])) {
      $schema->setSearchProperties($yaml[self::YAML_PARAM_SEARCH_PROPERTIES]);
    }

    if(isset($yaml[self::YAML_PARAM_OVERVIEW]) && is_array($yaml[self::YAML_PARAM_OVERVIEW])) {
      $schema->setEntityOverviewProperties($yaml[self::YAML_PARAM_OVERVIEW]);
    }

    if(isset($yaml[self::YAML_PARAM_EXT_OVERVIEW]) && is_array($yaml[self::YAML_PARAM_EXT_OVERVIEW])) {
      $schema->setExtendedEntityOverviewProperties($yaml[self::YAML_PARAM_EXT_OVERVIEW]);
    }
  }

  private function fillDatabaseDetails(EntitySchemaInterface $schema, array $yaml): void {
    if(!isset($yaml[self::YAML_PARAM_DATABASE_CONFIG])) {
      return;
    }

    $yaml = $yaml[self::YAML_PARAM_DATABASE_CONFIG];
    $schema->setBaseTable($yaml[self::YAML_PARAM_BASE_TABLE]);
    $database = DaViDatabaseOne::class;

    if($yaml[self::YAML_PARAM_DATABASE]){
      $database = match($yaml[self::YAML_PARAM_DATABASE]) {
        'one' => DaViDatabaseOne::class,
        'two' => DaViDatabaseTwo::class,
        default => $database,
      };
    }

    $schema->setDatabase($database);

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

}