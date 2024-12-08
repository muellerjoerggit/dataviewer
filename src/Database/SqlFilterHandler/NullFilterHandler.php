<?php

namespace App\Database\SqlFilterHandler;

use App\Database\DaViQueryBuilder;
use App\Database\SqlFilter\SqlFilter;
use App\Database\SqlFilter\SqlFilterDefinition;
use App\Database\SqlFilter\SqlFilterDefinitionInterface;
use App\Database\SqlFilter\SqlFilterHandlerInterface;
use App\Database\SqlFilter\SqlFilterInterface;
use App\Database\SqlFilter\SqlGeneratedFilterDefinition;
use App\DaViEntity\Schema\EntitySchema;

class NullFilterHandler implements SqlFilterHandlerInterface{

	public static function getNullFilterDefinition(): SqlFilterDefinitionInterface {
		$filterArray = [
			'name' => 'NullFilter',
			'title' => 'Null filter',
			'description' => 'Null filter',
			'handler' => 'NullFilterHandler',
		];

		return SqlFilterDefinition::createFromArray($filterArray);
	}

	public static function getNullFilter(): SqlFilterInterface {
		$filterDefinition = self::getNullFilterDefinition();
		return new SqlFilter($filterDefinition, null, '');
	}

	public function extendQueryWithFilter(DaViQueryBuilder $queryBuilder, SqlFilterInterface $filter, EntitySchema $schema): void {}

  public function getFilterComponent(SqlFilterDefinitionInterface $filterDefinition, EntitySchema $schema, string $filterKey = ''): array {
		return [];
	}

  public function buildFilterFromApi(SqlFilterDefinitionInterface $filterDefinition, mixed $filterValues, string $filterKey): SqlFilterInterface {
		return self::getNullFilter();
	}

  public function getGeneratedFilterComponent(SqlGeneratedFilterDefinition $filterDefinition, EntitySchema $schema, string $property): array {
    return [];
  }

}
