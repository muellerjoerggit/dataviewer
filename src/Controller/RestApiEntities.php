<?php

namespace App\Controller;

use App\Database\SqlFilter\SqlFilterBuilder;
use App\DaViEntity\DaViEntityManager;
use App\DaViEntity\EntityKey;
use App\DaViEntity\EntityViewBuilderInterface;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RestApiEntities extends AbstractController {

  #[Route(path: '/api/entities/search/overview/{client}', name: 'app_api_search_overview_list', methods: ['POST'])]
  public function searchOverviewList(
    Request $request,
    DaViEntityManager $entityManager,
    EntityTypeSchemaRegister $schemaRegister,
    SqlFilterBuilder $sqlFilterBuilder,
    string $client
  ): Response {
    $data = $request->toArray();
    $entityType = $data['entity_type'];
    $filters = $data['filters'];
    $schema = $schemaRegister->getEntityTypeSchema($entityType);
    $filterContainer = $sqlFilterBuilder->buildFilterContainerFromArray($client, $schema, $filters);
    if (!$schema->isSingleColumnPrimaryKeyInteger()) {
      $filterContainer->setLimit(500);
    }

    $entityList = $entityManager->getEntityListFromEntityType($entityType, $filterContainer);
    $entities = [];

    foreach ($entityList->iterateEntityList() as $entityKeyString => $entityData) {
      $entityKey = EntityKey::createFromString($entityKeyString);
      $overView['entityOverview'] = $entityManager->getExtendedEntityOverview($entityKey, [EntityViewBuilderInterface::FORMAT => FALSE]);
      $overView['entityKey'] = $entityKeyString;
      $overView['entityLabel'] = $entityData['entityLabel'];
      $entities[] = $overView;
    }

    $listArray = [
      'entities' => $entities,
      'entityCount' => $entityList->getTotalCount(),
      'lowerBound' => -1,
      'upperBound' => -1,
      'page' => $entityList->getPage(),
    ];

    if ($entityList->isUseBound()) {
      $listArray['lowerBound'] = $entityList->getLowerBound();
      $listArray['upperBound'] = $entityList->getUpperBound();
    }

    return $this->json($listArray);
  }

  #[Route(path: '/api/entities/{entityKey}', name: 'app_api_get_entity', methods: ['GET'])]
  public function getEntityByEntityKeyApi(DaViEntityManager $entityManager, string $entityKey): Response {
    $entityKey = EntityKey::createFromString($entityKey);
    $entity = $entityManager->getEntity($entityKey);
    $preRendered = $entityManager->preRenderEntity($entity);

    return $this->json($preRendered);
  }

  #[Route(path: '/api/entities/search/{client}/{entityType}/{searchString}', name: 'app_api_search_entity')]
  public function searchEntityList(DaViEntityManager $entityManager, string $client, string $entityType, string $searchString = ''): Response {
    $searchResult = $entityManager->getEntityListFromSearchString($client, $entityType, $searchString);
    return $this->json($searchResult);
  }

}