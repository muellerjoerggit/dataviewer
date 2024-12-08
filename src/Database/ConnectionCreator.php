<?php

namespace App\Database;

use App\Services\ClientService;
use App\Services\Environment;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class ConnectionCreator {

  public function __construct(
    private readonly ClientService $clientService,
    private readonly Environment $environment
  ) {}

  protected function getDbConnectionParam(): array {
    return [
      'user' => $this->environment->getDatabaseUser(),
      'password' => $this->environment->getDatabasePassword(),
      'host' => $this->environment->getDatabaseHost(),
      'port' => $this->environment->getDatabasePort(),
      'charset ' => 'utf8mb4',
      'driver' => 'pdo_mysql',
    ];
  }

  public function createConnection(string $client, string $prefix = ''): Connection {
    $databaseName = $this->clientService->getClientDatabaseName($client, $prefix);
    $connectionParam = $this->getDbConnectionParam();
    $connectionParam['dbname'] = $databaseName;
    return DriverManager::getConnection($connectionParam);
  }

  public function createConnectionWithoutDbName(): Connection {
    $connectionParam = $this->getDbConnectionParam();
    return DriverManager::getConnection($connectionParam);
  }

}