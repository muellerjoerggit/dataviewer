<?php

namespace App\Database\SqlFilterHandler;

use App\Database\DaViQueryBuilder;
use App\Database\SqlFilter\SqlFilterDefinitionInterface;
use App\Database\SqlFilter\SqlFilterHandlerInterface;
use App\Database\SqlFilter\SqlFilterInterface;
use App\DaViEntity\EntityKey;
use App\DaViEntity\Schema\EntitySchema;
use Doctrine\DBAL\ArrayParameterType;

class EntityKeyFilterHandler extends AbstractFilterHandler implements SqlFilterHandlerInterface {

  public function extendQueryWithFilter(DaViQueryBuilder $queryBuilder, SqlFilterInterface $filter, EntitySchema $schema): void {
    $entityKeys = $filter->getValue();
    $options = $filter->getOptions();

    $notOperator = isset($options['not_operator']) && $options['not_operator'];
    $this->setWhereIn($queryBuilder, $schema, $entityKeys, $notOperator);
  }

  private function setWhereIn(DaViQueryBuilder $queryBuilder, EntitySchema $schema, array $entityKeys, bool $notOperator = FALSE): void {
    $entityTypes = [];
    $uniqueIdentifiers = [];
    $outerExpressions = [];

    foreach ($entityKeys as $entityKey) {
      if (!($entityKey instanceof EntityKey)) {
        continue;
      }

      $entityTypes[$entityKey->getEntityType()] = NULL;

      $uniqueIdentifiers = array_merge_recursive($uniqueIdentifiers, $entityKey->getUniqueIdentifiers());
    }

    if (count($entityTypes) != 1) {
      return;
    }

    foreach ($uniqueIdentifiers as $uniqueIdentifier) {
      $innerExpressions = [];
      foreach ($uniqueIdentifier->iterateIdentifier() as $property => $propertyValues) {
        $table = $schema->getBaseTable();

        if (!is_scalar($property) || empty($table)) {
          continue;
        }

        $type = $this->resolveType($propertyValues);
        if ($notOperator) {
          $innerExpressions[] = $queryBuilder->expr()
            ->notIn($table . '.' . $property, ':inValues_' . $property);
        } else {
          $innerExpressions[] = $queryBuilder->expr()
            ->in($table . '.' . $property, ':inValues_' . $property);
        }

        $propertyValues = is_array($propertyValues) ? $propertyValues : [$propertyValues];
        $queryBuilder->setParameter('inValues_' . $property, $propertyValues, $type);
      }
      $outerExpressions[] = $queryBuilder->expr()->and(...$innerExpressions);
    }

    $queryBuilder->andWhere($queryBuilder->expr()->or(...$outerExpressions));
  }

  private function resolveType(mixed $values): int {
    if (is_string($values)) {
      return ArrayParameterType::STRING;
    } elseif (is_int($values)) {
      return ArrayParameterType::INTEGER;
    } elseif (is_array($values)) {
      $type = ArrayParameterType::INTEGER;
      foreach ($values as $value) {
        if (is_string($value)) {
          $type = ArrayParameterType::STRING;
          break;
        }
      }
      return $type;
    }
    return ArrayParameterType::STRING;
  }

  public function buildFilterFromApi(SqlFilterDefinitionInterface $filterDefinition, mixed $filterValues, string $filterKey): SqlFilterInterface {
    return NullFilterHandler::getNullFilter();
  }

}
