<?php

namespace App\Database\Aggregation;

use App\Database\QueryBuilder\DaViQueryBuilder;
use App\Database\QueryBuilder\QueryBuilderInterface;
use App\DaViEntity\Schema\EntitySchema;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('aggregation_handler')]
interface AggregationHandlerInterface {

  public const string YAML_PARAM_COUNT_COLUMN = 'count_column';
  public const string YAML_PARAM_PROPERTY_BLACKLIST = 'propertyBlacklist';

  public function processingAggregatedData(QueryBuilderInterface $queryBuilder, EntitySchema $schema, AggregationConfiguration $aggregationConfiguration): mixed;

  public function buildAggregatedQueryBuilder(EntitySchema $schema, QueryBuilderInterface $queryBuilder, AggregationConfiguration $aggregationConfiguration, array $options = []): void;

}
