<?php

namespace App\Database\Aggregation;

use App\Database\DaViQueryBuilder;
use App\DataCollections\TableData;
use App\DaViEntity\Schema\EntitySchema;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('aggregation_handler')]
interface AggregationHandlerInterface {

	public function processingAggregatedData(DaViQueryBuilder $queryBuilder, EntitySchema $schema, AggregationConfiguration $aggregationConfiguration): mixed;

	public function buildAggregatedQueryBuilder(
		EntitySchema $schema,
		DaViQueryBuilder $queryBuilder,
		AggregationConfiguration $aggregationConfiguration,
		array $columnsBlacklist = []
	): void;

}
