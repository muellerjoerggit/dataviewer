<?php

namespace App\Database\AggregationHandler;

use App\Database\Aggregation\AggregationConfiguration;
use App\Database\DaViQueryBuilder;
use App\DaViEntity\EntityDataMapperInterface;
use App\DaViEntity\Schema\EntitySchema;

class CountAggregationHandler extends AbstractAggregationHandler {

	public function buildAggregatedQueryBuilder(EntitySchema $schema, DaViQueryBuilder $queryBuilder, AggregationConfiguration $aggregationConfiguration, array $columnsBlacklist = []): void {
		$header = $aggregationConfiguration->getSetting('header');

		$columnCount = $header['count_column'] ?? 'count_column';
		$queryBuilder->select('COUNT(*) AS ' . $columnCount);
	}

	public function processingAggregatedData(DaViQueryBuilder $queryBuilder, EntitySchema $schema, AggregationConfiguration $aggregationConfiguration): mixed {
    return $this->executeQueryBuilder($queryBuilder, [EntityDataMapperInterface::OPTION_FETCH_TYPE => EntityDataMapperInterface::FETCH_TYPE_ONE], 0);
	}

}
