<?php

namespace App\Database\QueryBuilder;

use Doctrine\DBAL\ParameterType;

class NullQueryBuilder implements QueryBuilderInterface {

  public static function create(): QueryBuilderInterface {
    return new static();
  }

  public function getClient(): string {
    return '';
  }

  public function leftJoin($fromAlias, $join, $alias, $condition = NULL) {
    return $this;
  }

  public function rightJoin($fromAlias, $join, $alias, $condition = NULL) {
    return $this;
  }

  public function innerJoin($fromAlias, $join, $alias, $condition = NULL) {
    return $this;
  }

  public function addGroupBy($groupBy) {
    return $this;
  }

  public function getSQL() {
    return 'SELECT "" AS empty FROM DUAL;';
  }

  public function fetchAllAssociativeGroupIndexed(): array {
    return [];
  }

  public function expr() {
    return new NullExpressionBuilder();
  }

  public function fetchAssociative() {
    return [];
  }

  public function fetchNumeric() {
    return [];
  }

  public function fetchOne() {
    return 0;
  }

  public function fetchAllNumeric(): array {
    return [];
  }

  public function fetchAllAssociative(): array {
    return [];
  }

  public function fetchAllKeyValue(): array {
    return [];
  }

  public function fetchAllAssociativeIndexed(): array {
    return [];
  }

  public function fetchFirstColumn(): array {
    return [];
  }

  public function executeStatement(): int {
    return 0;
  }

  public function execute() {}

  public function setParameter($key, $value, $type = ParameterType::STRING) {}

  public function setParameters(array $params, array $types = []) {}

  public function getParameters() {}

  public function getParameter($key) {}

  public function getParameterTypes() {}

  public function getParameterType($key) {}

  public function getFirstResult() {}

  public function setMaxResults($maxResults) {}

  public function getMaxResults() {
    return 1;
  }

  public function add($sqlPartName, $sqlPart, $append = FALSE) {}

  public function select($select = NULL) {}

  public function distinct() {}

  public function addSelect($select = NULL) {}

  public function from($from, $alias = NULL) {}

  public function where($predicates) {}

  public function andWhere($where) {}

  public function orWhere($where) {}

  public function having($having) {}

  public function andHaving($having) {}

  public function orHaving($having) {}

  public function orderBy($sort, $order = NULL) {}

  public function addOrderBy($sort, $order = NULL) {}

  public function resetWhere() {}

  public function resetGroupBy() {}

  public function resetHaving() {}

  public function resetOrderBy() {}

}