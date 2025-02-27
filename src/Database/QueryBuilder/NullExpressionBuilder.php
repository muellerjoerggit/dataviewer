<?php

namespace App\Database\QueryBuilder;

use Doctrine\DBAL\Query\Expression\CompositeExpression;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;

class NullExpressionBuilder implements ExpressionBuilderInterface {

  public function and($expression, ...$expressions): CompositeExpression {
    return new CompositeExpression(CompositeExpression::TYPE_OR);
  }

  public function or($expression, ...$expressions): CompositeExpression {
    return new CompositeExpression(CompositeExpression::TYPE_OR);
  }

  public function comparison($x, $operator, $y): string {
    return '';
  }

  public function eq($x, $y): string {
    return '';
  }

  public function neq($x, $y): string {
    return '';
  }

  public function lt($x, $y) {
    // TODO: Implement lt() method.
  }

  public function lte($x, $y) {
    // TODO: Implement lte() method.
  }

  public function gt($x, $y) {
    // TODO: Implement gt() method.
  }

  public function gte($x, $y) {
    // TODO: Implement gte() method.
  }

  public function isNull($x) {
    // TODO: Implement isNull() method.
  }

  public function isNotNull($x) {
    // TODO: Implement isNotNull() method.
  }

  public function like($x, $y) {
    // TODO: Implement like() method.
  }

  public function notLike($x, $y) {
    // TODO: Implement notLike() method.
  }

  public function in($x, $y) {
    // TODO: Implement in() method.
  }

  public function notIn($x, $y) {
    // TODO: Implement notIn() method.
  }

  public function literal($input, $type = NULL) {
    // TODO: Implement literal() method.
  }

  public function getDoctrineExpressionBuilder(): ExpressionBuilder | null {
    return null;
  }

}