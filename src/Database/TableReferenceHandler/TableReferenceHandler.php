<?php

namespace App\Database\TableReferenceHandler;


use App\Database\DatabaseLocator;
use App\Database\DaViQueryBuilder;
use App\Database\TableReference\TableReferenceHandlerInterface;
use App\Database\TableReference\TableReferenceConfigurationInterface;
use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;

class TableReferenceHandler implements TableReferenceHandlerInterface {

	public function __construct(
    protected readonly EntityTypeSchemaRegister $schemaRegister,
    protected readonly DatabaseLocator $databaseLocator
  ) {}

	public function joinTableToQueryBuilder(DaViQueryBuilder $queryBuilder, TableReferenceConfigurationInterface $tableReferenceConfiguration, EntitySchema $fromSchema): void {
		$innerJoin = $tableReferenceConfiguration->getSetting('innerJoin', false);
		$toEntityType = $tableReferenceConfiguration->getSetting('entityType', '');

    if(empty($toEntityType)) {
      return;
    }

    $toSchema = $this->schemaRegister->getEntityTypeSchema($toEntityType);
    $toTable = $toSchema->getBaseTable();
    $toTableDatabase = $toTable;
    $toDatabase = $toSchema->getDatabase();
    $fromDatabase = $fromSchema->getDatabase();
    if($toDatabase !== $fromDatabase) {
      $databaseName = $this->databaseLocator->getDatabaseName($queryBuilder->getClient(), $toSchema);
      if(empty($databaseName)) {
        return;
      }
      $toTableDatabase = $databaseName . '.' . $toTable;
    }

		$alias = $tableReferenceConfiguration->getName();

		$fromTable = $fromSchema->getBaseTable();
		$condition = $this->buildCondition($fromTable, $alias, $tableReferenceConfiguration, $fromSchema);

		if(empty($condition) || empty($alias)) {
			return;
		}

		if($innerJoin) {
			$queryBuilder->innerJoin($fromTable, $toTableDatabase, $alias, $condition);
		} else {
			$queryBuilder->leftJoin($fromTable, $toTableDatabase, $alias, $condition);
		}
	}

	protected function buildCondition(string $fromTable, string $toTable, TableReferenceConfigurationInterface $additionalTableConfiguration, EntitySchema $fromSchema): string {
		$mapping = $additionalTableConfiguration->getSetting('condition_mapping', []);
		$fromTableColumn = key($mapping);
		$toTableColumn = current($mapping);

		if(empty($fromTableColumn) || empty($toTableColumn) || empty($fromTable) || empty($toTable)) {
			return '';
		}

		if(str_contains($fromTableColumn, '.')) {
			$leftCondition = $fromTableColumn;
		} else {
			$leftCondition = $fromTable . '.' . $fromTableColumn;
		}

		return $leftCondition . ' = ' . $toTable . '.' . $toTableColumn;
	}
}
