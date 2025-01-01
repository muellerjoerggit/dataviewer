<?php

namespace App\DaViEntity;

use App\Database\Aggregation\AggregationConfiguration;
use App\Database\DaViQueryBuilder;
use App\Database\SqlFilter\FilterContainer;
use App\DataCollections\EntityList;
use App\DaViEntity\Schema\EntitySchema;

interface EntityDataMapperInterface {

  public const string OPTION_WITH_COLUMNS = 'withColumns';
  public const string OPTION_WITH_JOINS = 'withJoins';
  public const string OPTION_LIMIT = 'limit';
  public const string OPTION_COLUMNS = 'propertyColumns';

  public const int FETCH_TYPE_ALL_ASSOCIATIVE = 1;
  public const int FETCH_TYPE_KEY_VALUE = 2;
  public const int FETCH_TYPE_ONE = 3;
  public const int FETCH_TYPE_ASSOCIATIVE_INDEXED = 4;
  public const int FETCH_TYPE_ASSOCIATIVE_GROUP_INDEXED = 5;
  public const int FETCH_TYPE_ASSOCIATIVE = 6;

  public const string OPTION_FETCH_TYPE = 'fetchType';

  public function buildQueryFromSchema(EntitySchema $schema, string $client, array $options = []): DaViQueryBuilder;

  public function fetchEntityData(EntitySchema $schema, FilterContainer $filters, array $options = []): array;

  public function fetchAggregatedData(string $client, EntitySchema $schema, AggregationConfiguration $aggregationConfiguration, FilterContainer $filterContainer = NULL): mixed;

  public function getEntityList(EntitySchema $schema, FilterContainer $filterContainer): EntityList;

  public function buildEntityKeyColumn(DaViQueryBuilder $queryBuilder, EntitySchema $schema): void;

  public function buildLabelColumn(DaViQueryBuilder $queryBuilder, EntitySchema $schema, bool $withEntityLabel = false): void;

}