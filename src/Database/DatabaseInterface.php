<?php

namespace App\Database;

use App\Database\QueryBuilder\QueryBuilderInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('services.database.database_interface')]
interface DatabaseInterface {

  public const string DB_DATETIME_FORMAT = 'Y-m-d H:i:s';

  public const string DB_DATE_FORMAT = 'Y-m-d';

  public function getDatabaseName(string $client): string;

  public function getConnection(string $client): Connection;

  public function createQueryBuilder(string $client): QueryBuilderInterface;

  public function tableExists(string $client, string $tableName): bool;

  public function createSchemaManager(string $client): AbstractSchemaManager|bool;

  public function fetchAssociativeFromSql(string $client, string $sql): array;

  public function fetchAssociativeFromQueryBuilder(QueryBuilderInterface $queryBuilder): array;

  public function getCountResultFromQueryBuilder(QueryBuilderInterface $queryBuilder): mixed;

}