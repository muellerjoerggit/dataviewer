<?php

namespace App\Database\QueryBuilder;

use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\Types\Type;

interface QueryBuilderInterface {

  public function getClient(): string;

  public function leftJoin($fromAlias, $join, $alias, $condition = NULL);

  public function rightJoin($fromAlias, $join, $alias, $condition = NULL);

  public function innerJoin($fromAlias, $join, $alias, $condition = NULL);

  public function addGroupBy($groupBy);

  public function getSQL();

  public function fetchAllAssociativeGroupIndexed(): array;

  /**
   * Gets an ExpressionBuilder used for object-oriented construction of query
   * expressions. This producer method is intended for convenient inline usage.
   * Example:
   *
   * <code>
   *     $qb = $conn->createQueryBuilder()
   *         ->select('u')
   *         ->from('users', 'u')
   *         ->where($qb->expr()->eq('u.id', 1));
   * </code>
   *
   * For more complex expression construction, consider storing the expression
   * builder object in a local variable.
   *
   * @return ExpressionBuilder
   */
  public function expr();

  /**
   * Prepares and executes an SQL query and returns the first row of the result
   * as an associative array.
   *
   * @return array<string, mixed>|false False is returned if no rows are found.
   *
   * @throws Exception
   */
  public function fetchAssociative();

  /**
   * Prepares and executes an SQL query and returns the first row of the result
   * as a numerically indexed array.
   *
   * @return array<int, mixed>|false False is returned if no rows are found.
   *
   * @throws Exception
   */
  public function fetchNumeric();

  /**
   * Prepares and executes an SQL query and returns the value of a single column
   * of the first row of the result.
   *
   * @return mixed|false False is returned if no rows are found.
   *
   * @throws Exception
   */
  public function fetchOne();

  /**
   * Prepares and executes an SQL query and returns the result as an array of
   * numeric arrays.
   *
   * @return array<int,array<int,mixed>>
   *
   * @throws Exception
   */
  public function fetchAllNumeric(): array;

  /**
   * Prepares and executes an SQL query and returns the result as an array of
   * associative arrays.
   *
   * @return array<int,array<string,mixed>>
   *
   * @throws Exception
   */
  public function fetchAllAssociative(): array;

  /**
   * Prepares and executes an SQL query and returns the result as an
   * associative array with the keys mapped to the first column and the values
   * mapped to the second column.
   *
   * @return array<mixed,mixed>
   *
   * @throws Exception
   */
  public function fetchAllKeyValue(): array;

  /**
   * Prepares and executes an SQL query and returns the result as an
   * associative array with the keys mapped to the first column and the values
   * being an associative array representing the rest of the columns and their
   * values.
   *
   * @return array<mixed,array<string,mixed>>
   *
   * @throws Exception
   */
  public function fetchAllAssociativeIndexed(): array;

  /**
   * Prepares and executes an SQL query and returns the result as an array of
   * the first column values.
   *
   * @return array<int,mixed>
   *
   * @throws Exception
   */
  public function fetchFirstColumn(): array;

  /**
   * Sets a query parameter for the query being constructed.
   *
   * <code>
   *     $qb = $conn->createQueryBuilder()
   *         ->select('u')
   *         ->from('users', 'u')
   *         ->where('u.id = :user_id')
   *         ->setParameter('user_id', 1);
   * </code>
   *
   * @param int|string $key Parameter position or name
   * @param mixed $value Parameter value
   * @param int|string|Type|null $type Parameter type
   *
   * @return $this This QueryBuilder instance.
   */
  public function setParameter($key, $value, $type = ParameterType::STRING);

  /**
   * Sets a collection of query parameters for the query being constructed.
   *
   * <code>
   *     $qb = $conn->createQueryBuilder()
   *         ->select('u')
   *         ->from('users', 'u')
   *         ->where('u.id = :user_id1 OR u.id = :user_id2')
   *         ->setParameters(array(
   *             'user_id1' => 1,
   *             'user_id2' => 2
   *         ));
   * </code>
   *
   * @param list<mixed>|array<string, mixed> $params Parameters to set
   * @param array<int, int|string|Type|null>|array<string, int|string|Type|null>
   *   $types Parameter types
   *
   * @return $this This QueryBuilder instance.
   */
  public function setParameters(array $params, array $types = []);

  /**
   * Gets all defined query parameters for the query being constructed indexed
   * by parameter index or name.
   *
   * @return list<mixed>|array<string, mixed> The currently defined query
   *   parameters
   */
  public function getParameters();

  /**
   * Gets a (previously set) query parameter of the query being constructed.
   *
   * @param mixed $key The key (index or name) of the bound parameter.
   *
   * @return mixed The value of the bound parameter.
   */
  public function getParameter($key);

  /**
   * Gets all defined query parameter types for the query being constructed
   * indexed by parameter index or name.
   *
   * @return array<int, int|string|Type|null>|array<string,
   *   int|string|Type|null> The currently defined query parameter types
   */
  public function getParameterTypes();

  /**
   * Gets a (previously set) query parameter type of the query being
   * constructed.
   *
   * @param int|string $key The key of the bound parameter type
   *
   * @return int|string|Type The value of the bound parameter type
   */
  public function getParameterType($key);

  /**
   * Gets the position of the first result the query object was set to retrieve
   * (the "offset").
   *
   * @return int The position of the first result.
   */
  public function getFirstResult();

  /**
   * Sets the maximum number of results to retrieve (the "limit").
   *
   * @param int|null $maxResults The maximum number of results to retrieve or
   *   NULL to retrieve all results.
   *
   * @return $this This QueryBuilder instance.
   */
  public function setMaxResults($maxResults);

  /**
   * Gets the maximum number of results the query object was set to retrieve
   * (the "limit"). Returns NULL if all results will be returned.
   *
   * @return int|null The maximum number of results.
   */
  public function getMaxResults();

  /**
   * Specifies an item that is to be returned in the query result.
   * Replaces any previously specified selections, if any.
   *
   * USING AN ARRAY ARGUMENT IS DEPRECATED. Pass each value as an individual
   * argument.
   *
   * <code>
   *     $qb = $conn->createQueryBuilder()
   *         ->select('u.id', 'p.id')
   *         ->from('users', 'u')
   *         ->leftJoin('u', 'phonenumbers', 'p', 'u.id = p.user_id');
   * </code>
   *
   * @param string|string[]|null $select The selection expression. USING AN
   *   ARRAY OR NULL IS DEPRECATED. Pass each value as an individual argument.
   *
   * @return $this This QueryBuilder instance.
   */
  public function select($select = NULL);

  /**
   * Adds or removes DISTINCT to/from the query.
   *
   * <code>
   *     $qb = $conn->createQueryBuilder()
   *         ->select('u.id')
   *         ->distinct()
   *         ->from('users', 'u')
   * </code>
   *
   * @return $this This QueryBuilder instance.
   */
  public function distinct();

  /**
   * Adds an item that is to be returned in the query result.
   *
   * USING AN ARRAY ARGUMENT IS DEPRECATED. Pass each value as an individual
   * argument.
   *
   * <code>
   *     $qb = $conn->createQueryBuilder()
   *         ->select('u.id')
   *         ->addSelect('p.id')
   *         ->from('users', 'u')
   *         ->leftJoin('u', 'phonenumbers', 'u.id = p.user_id');
   * </code>
   *
   * @param string|string[]|null $select The selection expression. USING AN
   *   ARRAY OR NULL IS DEPRECATED. Pass each value as an individual argument.
   *
   * @return $this This QueryBuilder instance.
   */
  public function addSelect($select = NULL);

  /**
   * Creates and adds a query root corresponding to the table identified by the
   * given alias, forming a cartesian product with any existing query roots.
   *
   * <code>
   *     $qb = $conn->createQueryBuilder()
   *         ->select('u.id')
   *         ->from('users', 'u')
   * </code>
   *
   * @param string $from The table.
   * @param string|null $alias The alias of the table.
   *
   * @return $this This QueryBuilder instance.
   */
  public function from($from, $alias = NULL);

  /**
   * Adds one or more restrictions to the query results, forming a logical
   * conjunction with any previously specified restrictions.
   *
   * <code>
   *     $qb = $conn->createQueryBuilder()
   *         ->select('u')
   *         ->from('users', 'u')
   *         ->where('u.username LIKE ?')
   *         ->andWhere('u.is_active = 1');
   * </code>
   *
   * @param mixed $where The query restrictions.
   *
   * @return $this This QueryBuilder instance.
   * @see where()
   *
   */
  public function andWhere($where);

  /**
   * Adds one or more restrictions to the query results, forming a logical
   * disjunction with any previously specified restrictions.
   *
   * <code>
   *     $qb = $em->createQueryBuilder()
   *         ->select('u.name')
   *         ->from('users', 'u')
   *         ->where('u.id = 1')
   *         ->orWhere('u.id = 2');
   * </code>
   *
   * @param mixed $where The WHERE statement.
   *
   * @return $this This QueryBuilder instance.
   * @see where()
   *
   */
  public function orWhere($where);

  /**
   * Adds a restriction over the groups of the query, forming a logical
   * conjunction with any existing having restrictions.
   *
   * @param mixed $having The restriction to append.
   *
   * @return $this This QueryBuilder instance.
   */
  public function andHaving($having);

  /**
   * Adds a restriction over the groups of the query, forming a logical
   * disjunction with any existing having restrictions.
   *
   * @param mixed $having The restriction to add.
   *
   * @return $this This QueryBuilder instance.
   */
  public function orHaving($having);

  /**
   * Adds an ordering to the query results.
   *
   * @param string $sort The ordering expression.
   * @param string $order The ordering direction.
   *
   * @return $this This QueryBuilder instance.
   */
  public function addOrderBy($sort, $order = NULL);

  /**
   * Resets the WHERE conditions for the query.
   *
   * @return $this This QueryBuilder instance.
   */
  public function resetWhere();

  /**
   * Resets the grouping for the query.
   *
   * @return $this This QueryBuilder instance.
   */
  public function resetGroupBy();

  /**
   * Resets the HAVING conditions for the query.
   *
   * @return $this This QueryBuilder instance.
   */
  public function resetHaving();

  /**
   * Resets the ordering for the query.
   *
   * @return $this This QueryBuilder instance.
   */
  public function resetOrderBy();

}