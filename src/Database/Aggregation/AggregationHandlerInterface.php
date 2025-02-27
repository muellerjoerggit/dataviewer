<?php

namespace App\Database\Aggregation;

use App\Database\QueryBuilder\QueryBuilderInterface;
use App\Database\AggregationHandler\Attribute\AggregationDefinitionInterface;
use App\DataCollections\TableData;
use App\DaViEntity\Schema\EntitySchema;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('aggregation_handler')]
interface AggregationHandlerInterface {

  public const string OPTION_PROPERTY_BLACKLIST = 'blacklist';

  public function processingAggregatedData(QueryBuilderInterface $queryBuilder, EntitySchema $schema, AggregationDefinitionInterface $aggregationDefinition): TableData | int;

  public function buildAggregatedQueryBuilder(EntitySchema $schema, QueryBuilderInterface $queryBuilder, AggregationDefinitionInterface $aggregationDefinition, array $options = []): void;

}
