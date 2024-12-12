<?php

namespace App\Database\SqlFilter;

use App\DaViEntity\Schema\EntitySchema;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('sql_filter_handler')]
interface SqlFilterHandlerInterface {

	public function buildFilterFromApi(SqlFilterDefinitionInterface $filterDefinition, mixed $filterValues, string $filterKey): SqlFilterInterface;

	public function getFilterComponent(SqlFilterDefinitionInterface $filterDefinition, EntitySchema $schema): array;
}
