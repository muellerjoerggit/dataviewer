<?php

namespace App\Database\QueryBuilder;

use Doctrine\DBAL\Query\Expression\CompositeExpression;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;

interface ExpressionBuilderInterface {

  /**
   * Creates a conjunction of the given expressions.
   *
   * @param string|CompositeExpression $expression
   * @param string|CompositeExpression ...$expressions
   */
  public function and(string | CompositeExpression $expression, string | CompositeExpression ...$expressions): CompositeExpression;

  /**
   * Creates a disjunction of the given expressions.
   *
   * @param string|CompositeExpression $expression
   * @param string|CompositeExpression ...$expressions
   */
  public function or(string | CompositeExpression $expression, string | CompositeExpression ...$expressions): CompositeExpression;

  /**
   * Creates a comparison expression.
   *
   * @param mixed $x The left expression.
   * @param string $operator One of the ExpressionBuilder::* constants.
   * @param mixed $y The right expression.
   *
   * @return string
   */
  public function comparison($x, $operator, $y): string;

  /**
   * Creates an equality comparison expression with the given arguments.
   *
   * First argument is considered the left expression and the second is the
   * right expression. When converted to string, it will generated a <left
   * expr> = <right expr>. Example:
   *
   *     [php]
   *     // u.id = ?
   *     $expr->eq('u.id', '?');
   *
   * @param mixed $x The left expression.
   * @param mixed $y The right expression.
   *
   * @return string
   */
  public function eq($x, $y): string;

  /**
   * Creates a non equality comparison expression with the given arguments.
   * First argument is considered the left expression and the second is the
   * right expression. When converted to string, it will generated a <left
   * expr> <> <right expr>. Example:
   *
   *     [php]
   *     // u.id <> 1
   *     $q->where($q->expr()->neq('u.id', '1'));
   *
   * @param mixed $x The left expression.
   * @param mixed $y The right expression.
   *
   * @return string
   */
  public function neq($x, $y): string;

  /**
   * Creates a lower-than comparison expression with the given arguments.
   * First argument is considered the left expression and the second is the
   * right expression. When converted to string, it will generated a <left
   * expr> < <right expr>. Example:
   *
   *     [php]
   *     // u.id < ?
   *     $q->where($q->expr()->lt('u.id', '?'));
   *
   * @param mixed $x The left expression.
   * @param mixed $y The right expression.
   *
   * @return string
   */
  public function lt($x, $y);

  /**
   * Creates a lower-than-equal comparison expression with the given arguments.
   * First argument is considered the left expression and the second is the
   * right expression. When converted to string, it will generated a <left
   * expr> <= <right expr>. Example:
   *
   *     [php]
   *     // u.id <= ?
   *     $q->where($q->expr()->lte('u.id', '?'));
   *
   * @param mixed $x The left expression.
   * @param mixed $y The right expression.
   *
   * @return string
   */
  public function lte($x, $y);

  /**
   * Creates a greater-than comparison expression with the given arguments.
   * First argument is considered the left expression and the second is the
   * right expression. When converted to string, it will generated a <left
   * expr> > <right expr>. Example:
   *
   *     [php]
   *     // u.id > ?
   *     $q->where($q->expr()->gt('u.id', '?'));
   *
   * @param mixed $x The left expression.
   * @param mixed $y The right expression.
   *
   * @return string
   */
  public function gt($x, $y);

  /**
   * Creates a greater-than-equal comparison expression with the given
   * arguments. First argument is considered the left expression and the second
   * is the right expression. When converted to string, it will generated a
   * <left expr> >= <right expr>. Example:
   *
   *     [php]
   *     // u.id >= ?
   *     $q->where($q->expr()->gte('u.id', '?'));
   *
   * @param mixed $x The left expression.
   * @param mixed $y The right expression.
   *
   * @return string
   */
  public function gte($x, $y);

  /**
   * Creates an IS NULL expression with the given arguments.
   *
   * @param string $x The expression to be restricted by IS NULL.
   *
   * @return string
   */
  public function isNull($x);

  /**
   * Creates an IS NOT NULL expression with the given arguments.
   *
   * @param string $x The expression to be restricted by IS NOT NULL.
   *
   * @return string
   */
  public function isNotNull($x);

  /**
   * Creates a LIKE() comparison expression with the given arguments.
   *
   * @param string $x The expression to be inspected by the LIKE comparison
   * @param mixed $y The pattern to compare against
   *
   * @return string
   */
  public function like($x, $y);

  /**
   * Creates a NOT LIKE() comparison expression with the given arguments.
   *
   * @param string $x The expression to be inspected by the NOT LIKE comparison
   * @param mixed $y The pattern to compare against
   *
   * @return string
   */
  public function notLike($x, $y);

  /**
   * Creates an IN () comparison expression with the given arguments.
   *
   * @param string $x The SQL expression to be matched against the set.
   * @param string|string[] $y The SQL expression or an array of SQL
   *   expressions representing the set.
   *
   * @return string
   */
  public function in($x, $y);

  /**
   * Creates a NOT IN () comparison expression with the given arguments.
   *
   * @param string $x The SQL expression to be matched against the set.
   * @param string|string[] $y The SQL expression or an array of SQL
   *   expressions representing the set.
   *
   * @return string
   */
  public function notIn($x, $y);

  /**
   * Builds an SQL literal from a given input parameter.
   *
   * The usage of this method is discouraged. Use prepared statements
   * or {@see AbstractPlatform::quoteStringLiteral()} instead.
   *
   * @param mixed $input The parameter to be quoted.
   * @param int|null $type The type of the parameter.
   *
   * @return string
   */
  public function literal($input, $type = NULL);

  public function getDoctrineExpressionBuilder(): ExpressionBuilder | null;

}