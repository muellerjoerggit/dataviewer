<?php

namespace App\Controller;

use App\Database\SqlFilter\SqlFilterDefinitionInterface;
use App\Database\SqlFilter\SqlFilterHandlerLocator;
use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\DaViEntity\Schema\EntityTypesRegister;
use App\Services\ClientService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RestApiMain extends AbstractController {

  #[Route(path: '/api/clients', name: 'app_api_clients')]
  public function getClients(ClientService $clientService): Response {
    return $this->json($clientService->getClientsName());
  }

  #[Route(path: '/api/entityTypes', name: 'app_api_get_entity_types')]
  public function getEntityTypesApi(EntityTypesRegister $entityTypesRegister, EntityTypeSchemaRegister $schemaRegister, SqlFilterHandlerLocator $filterHandlerLocator): Response {
    $entityTypes = [];
    foreach($entityTypesRegister->iterateEntityTypes() as $entityType) {
      if($entityType === 'NullEntity') {
        continue;
      }
      $schema = $schemaRegister->getEntityTypeSchema($entityType);
      $entityType = [
        'type' => $entityType,
        'label' => $schema->getEntityLabel(),
        'description' => $schema->getDescription(),
        'filterDefinitions' => $this->buildFilters($schema, $filterHandlerLocator),
        'filterGroups' => $this->getFilterGroups($schema),
        'groupFilterMapping' => $schema->getGroupFilterMappings(),
        'entityActions' => []
      ];

      $entityTypes[] = $entityType;
    }

    return $this->json($entityTypes);
  }

  private function getFilterGroups(EntitySchema $schema): array {
    $ret = [];
    foreach ($schema->iterateFilterGroups() as $filterGroup) {
      $ret[] = $filterGroup->getAsArray();
    }

    return $ret;
  }

  private function buildFilters(EntitySchema $schema, SqlFilterHandlerLocator $filterHandlerLocator): array {
    $ret = [];

    foreach($schema->iterateFilterDefinitions() as $filterKey => $filterDefinition) {
      $filter = $this->buildFilter($filterDefinition, $schema, $filterHandlerLocator);
      if(!empty($filter)) {
        $ret[$filterKey] = $filter;
      }
    }


    return $ret;
  }

  private function buildFilter(SqlFilterDefinitionInterface $filterDefinition, EntitySchema $schema, SqlFilterHandlerLocator $filterHandlerLocator): array {
    $handler = $filterHandlerLocator->getFilterHandlerFromFilterDefinition($filterDefinition);

    $component = $handler->getFilterComponent($filterDefinition, $schema);

    if(!empty($component)) {
      return $component;
    }

    return [];
  }

}