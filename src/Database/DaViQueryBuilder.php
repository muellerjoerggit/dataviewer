<?php

namespace App\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Result;

class DaViQueryBuilder extends QueryBuilder {

	private array $joinsOnHold = [];
	private array $groupByOnHold = [];
	private string $client;


	public function __construct(Connection $connection, string $client) {
		parent::__construct($connection);
		$this->client = $client;
	}

	public function getClient(): string	{
		return $this->client;
	}

	public function leftJoin($fromAlias, $join, $alias, $condition = null) {
		$this->joinsOnHold[$alias] = [
			$fromAlias => [
				'joinType'      => 'left',
				'joinTable'     => $join,
				'joinAlias'     => $alias,
				'joinCondition' => $condition,
			]];

		return $this;
	}

	public function innerJoin($fromAlias, $join, $alias, $condition = null)	{
		$this->joinsOnHold[$alias] = [
			$fromAlias => [
				'joinType'      => 'inner',
				'joinTable'     => $join,
				'joinAlias'     => $alias,
				'joinCondition' => $condition,
			]];

		return $this;
	}

	public function rightJoin($fromAlias, $join, $alias, $condition = null)	{
		$this->joinsOnHold[$alias] = [
			$fromAlias => [
				'joinType'      => 'left',
				'joinTable'     => $join,
				'joinAlias'     => $alias,
				'joinCondition' => $condition,
			]];

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

	public function executeQuery(): Result {
		$this->prepareSql();
		return parent::executeQuery();
	}

	public function getSQL() {
		$this->prepareSql();
		return parent::getSQL();
	}

	private function prepareSql(): void {
		foreach ($this->joinsOnHold as $key => $join) {
			$fromAlias = key($join);
			$join = current($join);

			switch ($join['joinType']) {
				case 'left':
					parent::leftJoin($fromAlias, $join['joinTable'], $join['joinAlias'], $join['joinCondition']);
					break;
				case 'right':
					parent::rightJoin($fromAlias, $join['joinTable'], $join['joinAlias'], $join['joinCondition']);
					break;
				case 'inner':
				default:
					parent::innerJoin($fromAlias, $join['joinTable'], $join['joinAlias'], $join['joinCondition']);
			}
		}

		$groupBys = array_unique($this->groupByOnHold);

		foreach ($groupBys as $groupBy) {
			parent::addGroupBy($groupBy);
		}

		$this->resetOnHold();
	}

	private function resetOnHold(): void {
		$this->groupByOnHold = [];
		$this->joinsOnHold = [];
	}

	public function fetchAllAssociativeGroupIndexed(): array {
		$result = parent::fetchAllAssociative();
		$ret = [];

		foreach($result as $row) {
			$firstColumn = array_shift($row);
			$ret[$firstColumn][] = $row;
		}

		return $ret;
	}

}
