<?php

namespace App\Database\QueryBuilder;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Result;

class DaViQueryBuilder implements QueryBuilderInterface {

  private array $joinsOnHold = [];

  private array $groupByOnHold = [];

  private string $client;

  private QueryBuilder $query;

  public function __construct(Connection $connection, string $client) {
    $this->query = $connection->createQueryBuilder();
    $this->client = $client;
  }

  public function getClient(): string {
    return $this->client;
  }

  public function executeQuery(): Result {
    $this->prepareSql();
    return $this->query->executeQuery();
  }

  /**
   * Mostly deprecated. The originally intend of joinsOnHold was the problem, that different handlers were adding the same sql to the query builder - because there was no way
   * to check, whether a join was already there
   */
  private function prepareSql(): void {
    foreach ($this->joinsOnHold as $key => $join) {
      $fromAlias = key($join);
      $join = current($join);

      switch ($join['joinType']) {
        case 'left':
          $this->query->leftJoin($fromAlias, $join['joinTable'], $join['joinAlias'], $join['joinCondition']);
          break;
        case 'right':
          $this->query->rightJoin($fromAlias, $join['joinTable'], $join['joinAlias'], $join['joinCondition']);
          break;
        case 'inner':
        default:
        $this->query->innerJoin($fromAlias, $join['joinTable'], $join['joinAlias'], $join['joinCondition']);
      }
    }

    $groupBys = array_unique($this->groupByOnHold);

    array_map(function($groupBy) {
      $this->query->addGroupBy($groupBy);
    }, $groupBys);

    $this->resetOnHold();
  }

  public function leftJoin($fromAlias, $join, $alias, $condition = NULL) {
    $this->joinsOnHold[$alias] = [
      $fromAlias => [
        'joinType' => 'left',
        'joinTable' => $join,
        'joinAlias' => $alias,
        'joinCondition' => $condition,
      ],
    ];

    return $this;
  }

  public function rightJoin($fromAlias, $join, $alias, $condition = NULL) {
    $this->joinsOnHold[$alias] = [
      $fromAlias => [
        'joinType' => 'left',
        'joinTable' => $join,
        'joinAlias' => $alias,
        'joinCondition' => $condition,
      ],
    ];

    return $this;
  }

  public function innerJoin($fromAlias, $join, $alias, $condition = NULL) {
    $this->joinsOnHold[$alias] = [
      $fromAlias => [
        'joinType' => 'inner',
        'joinTable' => $join,
        'joinAlias' => $alias,
        'joinCondition' => $condition,
      ],
    ];

    return $this;
  }

  public function addGroupBy($groupBy) {
    if (is_array($groupBy) && count($groupBy) === 0) {
      return $this;
    }

    $groupBy = is_array($groupBy) ? $groupBy : func_get_args();

    $this->groupByOnHold = array_merge($this->groupByOnHold, $groupBy);

    return $this;
  }

  private function resetOnHold(): void {
    $this->groupByOnHold = [];
    $this->joinsOnHold = [];
  }

  public function getSQL() {
    $this->prepareSql();
    return $this->query->getSQL();
  }

  public function fetchAllAssociativeGroupIndexed(): array {
    $this->prepareSql();
    $result = $this->query->fetchAllAssociative();
    $ret = [];

    foreach ($result as $row) {
      $firstColumn = array_shift($row);
      $ret[$firstColumn][] = $row;
    }

    return $ret;
  }

  public function expr() {
    return new DaViExpressionBuilder($this->query);
  }

  public function fetchAssociative() {
    $this->prepareSql();
    return $this->query->fetchAssociative();
  }

  public function fetchNumeric() {
    $this->prepareSql();
    return $this->query->fetchNumeric();
  }

  public function fetchOne() {
    $this->prepareSql();
    return $this->query->fetchOne();
  }

  public function fetchAllNumeric(): array {
    $this->prepareSql();
    return $this->query->fetchAllNumeric();
  }

  public function fetchAllAssociative(): array {
    $this->prepareSql();
    return $this->query->fetchAllAssociative();
  }

  public function fetchAllKeyValue(): array {
    $this->prepareSql();
    return $this->query->fetchAllKeyValue();
  }

  public function fetchAllAssociativeIndexed(): array {
    $this->prepareSql();
    return $this->query->fetchAllAssociativeIndexed();
  }

  public function fetchFirstColumn(): array {
    $this->prepareSql();
    return $this->query->fetchFirstColumn();
  }

  public function setParameter($key, $value, $type = ParameterType::STRING) {
    $this->query->setParameter($key, $value, $type);
    return $this;
  }

  public function setParameters(array $params, array $types = []) {
    $this->query->setParameters($params, $types);
    return $this;
  }

  public function getParameters() {
    return $this->query->getParameters();
  }

  public function getParameter($key) {
    return $this->query->getParameter($key);
  }

  public function getParameterTypes() {
    return $this->query->getParameterTypes();
  }

  public function getParameterType($key) {
    return $this->query->getParameterType($key);
  }

  public function getFirstResult() {
    $this->prepareSql();
    return $this->query->getFirstResult();
  }

  public function setMaxResults($maxResults) {
    $this->query->setMaxResults($maxResults);
    return $this;
  }

  public function getMaxResults() {
    return $this->query->getMaxResults();
  }

  public function select($select = NULL) {
    $this->query->select($select);
    return $this;
  }

  public function distinct() {
    $this->query->distinct();
    return $this;
  }

  public function addSelect($select = NULL) {
    $this->query->addSelect($select);
    return $this;
  }

  public function from($from, $alias = NULL) {
    $this->query->from($from, $alias);
    return $this;
  }

  public function andWhere($where) {
    if($where instanceof NullExpressionBuilder) {
      return $this;
    } elseif ($where instanceof DaViExpressionBuilder) {
      $where = $where->getDoctrineExpressionBuilder();
    }
    $this->query->andWhere($where);
    return $this;
  }

  public function orWhere($where) {
    if($where instanceof NullExpressionBuilder) {
      return $this;
    } elseif ($where instanceof DaViExpressionBuilder) {
      $where = $where->getDoctrineExpressionBuilder();
    }
    $this->query->orWhere($where);
    return $this;
  }

  public function andHaving($having) {
    if($having instanceof NullExpressionBuilder) {
      return $this;
    } elseif ($having instanceof DaViExpressionBuilder) {
      $having = $having->getDoctrineExpressionBuilder();
    }

    $this->query->andHaving($having);
    return $this;
  }

  public function orHaving($having) {
    if($having instanceof NullExpressionBuilder) {
      return $this;
    } elseif ($having instanceof DaViExpressionBuilder) {
      $having = $having->getDoctrineExpressionBuilder();
    }

    $this->query->orHaving($having);
    return $this;
  }

  public function addOrderBy($sort, $order = NULL) {
    $this->query->addOrderBy($sort, $order);
    return $this;
  }

  public function resetWhere() {
    $this->query->resetWhere();
    return $this;
  }

  public function resetGroupBy() {
    $this->query->resetGroupBy();
    return $this;
  }

  public function resetHaving() {
    $this->query->resetHaving();
    return $this;
  }

  public function resetOrderBy() {
    $this->query->resetOrderBy();
    return $this;
  }

}
