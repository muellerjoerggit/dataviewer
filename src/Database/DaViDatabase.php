<?php

namespace App\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryException;
use Doctrine\DBAL\Schema\AbstractSchemaManager;

class DaViDatabase implements DatabaseInterface {

	protected const string DATABASE_PREFIX = '';

	protected array $connections = [];

	public function __construct(
    private readonly ConnectionCreator $connectionCreator
  ) {}

	public function getDatabaseName(string $client): string {
		try {
			return $this->getConnection($client)->getDatabase();
		} catch (Exception $exception) {
			$this->logException($exception);
			return '';
		}
	}

	protected function createConnection(string $client): Connection {
    $connection = $this->connectionCreator->createConnection($client, static::DATABASE_PREFIX);
		$this->connections[$client] = $connection;
		return $connection;
	}

  public function createConnectionWithoutDbName(): Connection {
    return $this->connectionCreator->createConnectionWithoutDbName();
  }

	public function getConnection(string $client): Connection {
		if(isset($this->connections[$client])) {
			return $this->connections[$client];
		}

		return $this->createConnection($client);
	}

	public function createQueryBuilder(string $client): DaViQueryBuilder {
		return new DaViQueryBuilder($this->getConnection($client), $client);
	}

	public function tableExists(string $client, string $tableName): bool {
		$schemaManager = $this->createSchemaManager($client);

		if(!$schemaManager) {
			return false;
		}

		try {
			return $schemaManager->tablesExist([$tableName]);
		} catch (\Exception $exception) {
			return false;
		}
	}

	public function createSchemaManager(string $client): AbstractSchemaManager | bool {
		try {
			return $this->getConnection($client)->createSchemaManager();
		} catch (\Exception $exception) {
			return false;
		}
	}

	public function fetchAssociativeFromSql(string $client, string $sql): array {
		try {
			$stmt = $this->getConnection($client)->prepare($sql);
			return $stmt->executeQuery()->fetchAllAssociative();
		}
		catch (QueryException | Exception $exception) {
			$this->logException($exception);
			return [];
		}
	}

	public function fetchAssociativeFromQueryBuilder(DaViQueryBuilder $queryBuilder): array {
		try {
			return $queryBuilder->fetchAllAssociative();
		} catch (Exception $exception) {
			$this->logException($exception);
			return [];
		}
	}

	protected function logException(Exception $exception): void {
//		$logItem = LogItem::createLogItem('Datenbank Fehler', '', LogItemInterface::LOG_LEVEL_ERROR);
//		$logItem->addRawLogs($exception);
	}

	public function getCountResultFromQueryBuilder(DaViQueryBuilder $queryBuilder): int {
		$queryBuilder
			->select('1')
			->setMaxResults(null)
		;

		$sql = $queryBuilder->getSQL();
		$sql = 'SELECT COUNT(*) AS count_entities FROM (' . $sql . ') AS querybuilder;';
		$params = $queryBuilder->getParameters() ?? [];
		$types = $queryBuilder->getParameterTypes() ?? [];
		$client = $queryBuilder->getClient();

//		$sqlLogItem = SqlLogItem::createSqlLogItem($sql);

		try {
      return $this->getConnection($client)->executeQuery($sql, $params, $types)->fetchOne();
		} catch (\Exception $exception) {
//			$logItem = LogItem::createExceptionLogItem($exception);
//			$logItem->addRawLogs($sql);
			return 0;
		}
	}

}
