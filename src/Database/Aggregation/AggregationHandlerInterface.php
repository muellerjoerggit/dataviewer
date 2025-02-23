<?php

namespace App\Database\Aggregation;

use App\Database\AggregationHandler\Attribute\AggregationDefinitionInterface;
use App\Database\DaViQueryBuilder;
use App\DataCollections\TableData;
use App\DaViEntity\Schema\EntitySchema;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('aggregation_handler')]
interface AggregationHandlerInterface {

  public const string OPTION_PROPERTY_BLACKLIST = 'blacklist';

  public function processingAggregatedData(DaViQueryBuilder $queryBuilder, EntitySchema $schema, AggregationDefinitionInterface $aggregationDefinition): TableData | int;

  public function buildAggregatedQueryBuilder(EntitySchema $schema, DaViQueryBuilder $queryBuilder, AggregationDefinitionInterface $aggregationDefinition, array $options = []): void;

}
