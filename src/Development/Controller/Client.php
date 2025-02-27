<?php

namespace App\Development\Controller;

use App\Database\SymfonyDatabase;
use App\SymfonyRepository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Client extends AbstractController {

  public function getAllClients(ClientRepository $clientRepository) {
    $clients = $clientRepository->findAll();
    return $this->json($clients);
  }

  public function getFetchClients(SymfonyDatabase $database) {
    return $this->json($database->fetchAssociativeFromSql('SELECT * FROM client;'));
  }

}