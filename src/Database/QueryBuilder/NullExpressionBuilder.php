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

  public function lt($x, $y) {}

  public function lte($x, $y) {}

  public function gt($x, $y) {}

  public function gte($x, $y) {}

  public function isNull($x) {}

  public function isNotNull($x) {}

  public function like($x, $y) {}

  public function notLike($x, $y) {}

  public function in($x, $y) {}

  public function notIn($x, $y) {}

  public function literal($input, $type = NULL) {}

  public function getDoctrineExpressionBuilder(): ExpressionBuilder | null {
    return null;
  }

}