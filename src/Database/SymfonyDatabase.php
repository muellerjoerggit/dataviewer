<?php

namespace App\Database;

use App\Logger\Logger;
use App\Logger\LogItems\LogItem;
use App\Logger\LogItems\LogItemInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Query\QueryException;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\Persistence\ManagerRegistry;

class SymfonyDatabase {

  private Connection $connection;

  private Logger $logger;

  public function __construct(ManagerRegistry $doctrine, Logger $logger) {
    $this->connection = $doctrine->getManager()->getConnection();
    $this->logger = $logger;
  }

  public function getConnection(): Connection {
    return $this->connection;
  }

  public function getDatabaseName(): string {
    try {
      return $this->getConnection()->getDatabase();
    } catch (Exception $exception) {
      $this->logException($exception);
      return '';
    }
  }

  protected function logException(Exception $exception): void {
    $logItem = LogItem::createLogItem('Datenbank Fehler', '', LogItemInterface::LOG_LEVEL_ERROR);
    $logItem->addRawLogs($exception);
    $this->logger->addLog($logItem);
  }

  public function createQueryBuilder(): QueryBuilder {
    return $this->getConnection()->createQueryBuilder();
  }

  public function tableExists(string $tableName): bool {
    $schemaManager = $this->createSchemaManager();

    if (!$schemaManager) {
      return FALSE;
    }

    try {
      return $schemaManager->tablesExist([$tableName]);
    } catch (\Exception $exception) {
      return FALSE;
    }
  }

  public function createSchemaManager(): AbstractSchemaManager|bool {
    try {
      return $this->getConnection()->createSchemaManager();
    } catch (\Exception $exception) {
      return FALSE;
    }
  }

  public function fetchAssociativeFromSql(string $sql): array {
    try {
      $stmt = $this->getConnection()->prepare($sql);
      return $stmt->executeQuery()->fetchAllAssociative();
    } catch (QueryException|Exception $exception) {
      $this->logException($exception);
      return [];
    }
  }

  public function fetchAssociativeFromQueryBuilder(QueryBuilder $queryBuilder): array {
    try {
      return $queryBuilder->fetchAllAssociative();
    } catch (Exception $exception) {
      $this->logException($exception);
      return [];
    }
  }

}
