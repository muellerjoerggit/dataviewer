<?php

namespace App\Database\SqlFilterHandler;

use App\Database\QueryBuilder\DaViQueryBuilder;
use App\Database\QueryBuilder\QueryBuilderInterface;
use App\Database\SqlFilter\SqlFilter;
use App\Database\SqlFilter\SqlFilterDefinition;
use App\Database\SqlFilter\SqlFilterHandlerInterface;
use App\Database\SqlFilter\SqlFilterInterface;
use App\Database\SqlFilterHandler\Attribute\SqlFilterDefinitionAttr;
use App\Database\SqlFilterHandler\Attribute\SqlFilterDefinitionInterface;
use App\DaViEntity\Schema\EntitySchema;

class NullFilterHandler implements SqlFilterHandlerInterface{

	public static function getNullFilterDefinition(): SqlFilterDefinitionInterface {
    return new SqlFilterDefinitionAttr(
      NullFilterHandler::class,
      'NullFilterHandler',
      'Null filter',
      'Null Filter Fallback, wenn es irgendwelche Probleme mit den Filtern gibt',
    );
	}

	public static function getNullFilter(): SqlFilterInterface {
		$filterDefinition = self::getNullFilterDefinition();
		return new SqlFilter($filterDefinition, null, '');
	}

	public function extendQueryWithFilter(QueryBuilderInterface $queryBuilder, SqlFilterInterface $filter, EntitySchema $schema): void {}

  public function getFilterComponent(SqlFilterDefinitionInterface $filterDefinition, EntitySchema $schema): array {
		return [];
	}

  public function buildFilterFromApi(SqlFilterDefinitionInterface $filterDefinition, mixed $filterValues, string $filterKey): SqlFilterInterface {
		return self::getNullFilter();
	}

}
