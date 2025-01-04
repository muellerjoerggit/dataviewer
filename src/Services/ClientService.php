<?php

namespace App\Services;

use App\SymfonyRepository\ClientRepository;

class ClientService {

  private array $clients;

  public function __construct(
    private readonly ClientRepository $clientRepository
  ) {
    $this->initClients();
  }

  private function initClients(): void {
    $clients = $this->clientRepository->findAll();

    foreach ($clients as $client) {
      $clientId = $client->getClientId();
      $this->clients[$clientId] = [
        'clientId' => $clientId,
        'name' => $client->getName(),
        'databaseName' => $client->getDatabaseName(),
        'url' => $client->getUrl(),
      ];
    }
  }

  public function getFirstClientId(): string {
    $client = reset($this->clients);
    return $client['clientId'];
  }

  public function getClientsName(): array {
    return array_column($this->clients, 'name', 'clientId');
  }

  public function getClientName(string $client): string {
    return $this->clients[$client]['name'] ?? 'unbekannter Client';
  }

  public function getClientDatabaseName(string $client, string $preFix = ''): string {
    if (!$this->isClient($client)) {
      return '';
    }

    $databaseName = $this->clients[$client]['databaseName'];

    if (empty($databaseName)) {
      return '';
    }

    return empty($preFix) ? $databaseName : $preFix . $databaseName;
  }

  public function isClient(string $client): bool {
    return isset($this->clients[$client]);
  }

  public function getClientUrl(string $client): string {
    return $this->clients[$client]['url'] ?? '';
  }

}
