<?php

namespace App\Database\SqlFilterHandler;

use App\Database\DaViQueryBuilder;
use App\Database\SqlFilterHandler\Attribute\SqlFilterDefinitionInterface;
use App\Database\SqlFilter\SqlFilterInterface;
use App\Database\SqlFilterHandler\Attribute\SqlFilterEntityReferenceDefinitionAttr;
use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\Item\ItemHandler_EntityReference\EntityReferenceItemHandlerLocator;
use App\Item\ItemHandler_EntityReference\SimpleEntityReferenceJoinInterface;

class EntityReferenceFilterHandler extends AbstractFilterHandler implements InFilterInterface {

  protected const string COMPONENT_NAME = 'EntityReferenceFilter';

	public function __construct(
		protected readonly EntityReferenceItemHandlerLocator $referenceItemHandlerLocator,
		protected readonly EntityTypeSchemaRegister $schemaRegister,
	) {}

	public function setWhereIn(DaViQueryBuilder $queryBuilder, string $column, mixed $values, int $dataType): bool {
		$parameter = 'values_' . str_replace('.', '_', $column);

		$queryBuilder->andWhere(
			$queryBuilder->expr()->in($column, ':' . $parameter)
		);
		$queryBuilder->setParameter($parameter, $values, $dataType);

		return true;
	}

	public function extendQueryWithFilter(DaViQueryBuilder $queryBuilder, SqlFilterInterface $filter, EntitySchema $schema): void {
		$value = $filter->getValue();
    $property = $filter->getFilterDefinition()->getProperty();
		$column = $this->getColumn($filter, $schema);
    $dataType = $schema->getProperty($property)->getQueryParameterType(true);

		$this->setWhereIn($queryBuilder, $column, $value, $dataType);
	}

	public function getFilterComponent(SqlFilterDefinitionInterface $filterDefinition, EntitySchema $schema): array {
    if(!$filterDefinition instanceof SqlFilterEntityReferenceDefinitionAttr) {
      return [];
    }

		$property = $filterDefinition->getProperty();
		$config = $schema->getProperty($property);
    $handler = $this->referenceItemHandlerLocator->getEntityReferenceHandlerFromItem($config);

		if($filterDefinition->hasTargetEntity()) {
      $targetEntityClass = $filterDefinition->getTargetEntityClass();
		} elseif($handler instanceof SimpleEntityReferenceJoinInterface) {
      [$targetEntityClass, $property] = $handler->getTargetSetting($config);
    } else {
      return [];
    }

		$targetSchema = $this->schemaRegister->getSchemaFromEntityClass($targetEntityClass);

    $uniqueProperty = $targetSchema->getUniqueProperties();
    $uniqueProperty = reset($uniqueProperty);
    $uniqueProperty = reset($uniqueProperty);

		return $this->buildComponent(
      $filterDefinition,
      $targetSchema->getEntityLabel(),
      $targetSchema->getEntityType(),
      $uniqueProperty
    );
	}

	protected function buildComponent(SqlFilterDefinitionInterface $filterDefinition, string $entityTypeLabel, string $targetEntityType, string $uniqueProperty): array {
    $component = $this->getFilterComponentInternal($filterDefinition);

    $component['additional'] = [
      'entityTypeLabel' => $entityTypeLabel,
      'entityType' => $targetEntityType,
      'uniqueProperty' => $uniqueProperty,
    ];

		return $component;
	}

}
