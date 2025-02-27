<?php

namespace App\Database\QueryBuilder;

use Doctrine\DBAL\Query\Expression\CompositeExpression;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Doctrine\DBAL\Query\QueryBuilder;

class DaViExpressionBuilder implements ExpressionBuilderInterface {

  private ExpressionBuilder $expressionBuilder;

  public function __construct(
    QueryBuilder $queryBuilder
  ) {
    $this->expressionBuilder = $queryBuilder->expr();
  }

  public function and($expression, ...$expressions): CompositeExpression {
    return $this->expressionBuilder->and($expression, ...$expressions);
  }

  public function or($expression, ...$expressions): CompositeExpression {
    return $this->expressionBuilder->or($expression, ...$expressions);
  }

  public function comparison($x, $operator, $y): string {
    return $this->expressionBuilder->comparison($x, $operator, $y);
  }

  public function eq($x, $y): string {
    return $this->expressionBuilder->eq($x, $y);
  }

  public function neq($x, $y): string {
    return $this->expressionBuilder->neq($x, $y);
  }

  public function lt($x, $y) {
    return $this->expressionBuilder->lt($x, $y);
  }

  public function lte($x, $y) {
   return $this->expressionBuilder->lte($x, $y);
  }

  public function gt($x, $y) {
    return $this->expressionBuilder->gt($x, $y);
  }

  public function gte($x, $y) {
    return $this->expressionBuilder->gte($x, $y);
  }

  public function isNull($x) {
    return $this->expressionBuilder->isNull($x);
  }

  public function isNotNull($x) {
    return $this->expressionBuilder->isNotNull($x);
  }

  public function like($x, $y) {
    return $this->expressionBuilder->like($x, $y);
  }

  public function notLike($x, $y) {
    return $this->expressionBuilder->notLike($x, $y);
  }

  public function in($x, $y) {
    return $this->expressionBuilder->in($x, $y);
  }

  public function notIn($x, $y) {
    return $this->expressionBuilder->notIn($x, $y);
  }

  public function literal($input, $type = NULL) {
    return $this->expressionBuilder->literal($input, $type);
  }

  public function getDoctrineExpressionBuilder(): ExpressionBuilder | null {
    return $this->expressionBuilder;
  }

}