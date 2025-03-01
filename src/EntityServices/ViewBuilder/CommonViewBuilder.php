<?php

namespace App\EntityServices\ViewBuilder;

use App\DaViEntity\DaViEntityManager;
use App\DaViEntity\EntityInterface;
use App\EntityServices\OverviewBuilder\OverviewBuilderLocator;
use App\Item\ItemHandler_PreRendering\PreRenderingItemHandlerLocator;
use App\Logger\LogItemPreRendering\LogItemPreRenderingHandlerLocator;
use App\Logger\LogItems\LogItemInterface;
use App\Services\EntityAction\EntityActionPreRenderingBuilder;

class CommonViewBuilder implements ViewBuilderInterface {

  public function __construct(
    private readonly PreRenderingItemHandlerLocator $preRenderingItemHandlerLocator,
    private readonly DaViEntityManager $entityManager,
    private readonly LogItemPreRenderingHandlerLocator $logItemPreRenderingLocator,
    private readonly EntityActionPreRenderingBuilder $actionPreRenderingBuilder,
    private readonly OverviewBuilderLocator $overviewBuilderLocator,
  ) {}

  public function preRenderEntity(EntityInterface $entity): array {
    $propertiesRenderArray = [];

    foreach ($entity->getSchema()->iterateProperties() as $property => $config) {
      $item = $entity->getPropertyItem($property);
      $handler = $this->preRenderingItemHandlerLocator->getPreRenderingHandlerFromItem($item->getConfiguration());
      $propertiesRenderArray[] = $handler->getComponentPreRenderArray($entity, $property);
    }

    $logsByLevel = [
      'critical' => [],
      'warning' => [],
      'notice' => [],
      'info' => [],
      'error' => [],
      'debug' => [],
    ];

    foreach ($entity->getAllLogsByLogLevels() as $logLevel => $logItems) {
      foreach ($logItems as $logItem) {
        if (!($logItem instanceof LogItemInterface)) {
          continue;
        }
        $preRenderingHandler = $this->logItemPreRenderingLocator->getLogItemPreRenderingHandlerFromLogItem($logItem);
        $logsByLevel[$logLevel][] = $preRenderingHandler->preRenderLogItemComponent($logItem);
      }
    }

    return [
      'entityKey' => $entity->getEntityKeyAsObj()->getFirstEntityKeyAsString(),
      'label' => $this->entityManager->getEntityLabel($entity),
      'entityOverview' => $this->buildEntityOverview($entity),
      'extEntityOverview' => $this->buildExtendedEntityOverview($entity),
      'properties' => $propertiesRenderArray,
      'logsByLevel' => $logsByLevel,
      'entityActions' => $this->actionPreRenderingBuilder->buildEntityActions($entity),
    ];
  }

  private function buildEntityOverview(EntityInterface $entity): array {
    $overviewBuilder = $this->overviewBuilderLocator->getOverviewBuilder($entity->getSchema(), $entity->getClient());
    return $overviewBuilder->buildEntityOverview($entity);
  }

  private function buildExtendedEntityOverview(EntityInterface $entity): array {
    $overviewBuilder = $this->overviewBuilderLocator->getOverviewBuilder($entity->getSchema(), $entity->getClient());
    return $overviewBuilder->buildExtendedEntityOverview($entity);
  }

}
