<?php

namespace App\DaViEntity;

use App\Item\ItemHandler_PreRendering\PreRenderingItemHandlerLocator;
use App\Item\ItemHandler_ValueFormatter\ValueFormatterItemHandlerLocator;
use App\Item\ItemInterface;
use App\Logger\LogItemPreRendering\LogItemPreRenderingHandlerLocator;
use App\Logger\LogItems\LogItemInterface;

class CommonEntityViewBuilder implements EntityViewBuilderInterface {

  public function __construct(
    private readonly PreRenderingItemHandlerLocator $preRenderingItemHandlerLocator,
    private readonly DaViEntityManager $entityManager,
    private readonly ValueFormatterItemHandlerLocator $valueFormatterItemHandlerLocator,
    private readonly LogItemPreRenderingHandlerLocator $logItemPreRenderingLocator
  ) {}

  public function preRenderEntity(EntityInterface $entity): array {
    $propertiesRenderArray = [];
    $parameterRenderArray = [];

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
      'entityOverview' => $this->buildEntityOverview($entity),
      'label' => $this->entityManager->getEntityLabel($entity),
      'showReferences' => !empty($references),
      'references' => [],
      'showProperties' => !empty($propertiesRenderArray),
      'properties' => $propertiesRenderArray,
      'showParameters' => !empty($parameterRenderArray),
      'parameters' => $parameterRenderArray,
      'showLogs' => !empty($logsByLevel),
      'logsByLevel' => $logsByLevel,
      'entityActions' => [],
    ];
  }

  public function buildEntityOverview(EntityInterface $entity, array $options = []): array {
    $defaultOverview = $entity->getSchema()->getEntityOverviewProperties();
    $options = $this->getDefaultOverviewOptions($options, $entity, $defaultOverview);

    $header = [];
    $data = [];

    foreach ($options[EntityViewBuilderInterface::PROPERTIES] as $property => $title) {
      $items = $this->entityManager->getItemsFromPath($property, $entity);
      $item = reset($items);
      if (!($item instanceof ItemInterface)) {
        continue;
      }
      $itemConfiguration = $item->getConfiguration();
      $header[$property] = empty($title) ? $itemConfiguration->getLabel() : $title;
      $firstValue = $item->getFirstValueAsString();
      if ($options[EntityViewBuilderInterface::FORMAT] && $itemConfiguration->hasFormatterHandler()) {
        $handler = $this->valueFormatterItemHandlerLocator->getFormatterHandlerFromItem($itemConfiguration);
        $firstValue = $handler->getValueFormatted($itemConfiguration, $firstValue);
      }

      if (mb_strlen($firstValue) > 50) {
        $firstValue = mb_substr($firstValue, 0, 50) . ' ...';
      }
      $data[$property] = $firstValue;
    }

    return [
      'header' => $header,
      'data' => $data,
    ];
  }

  protected function getDefaultOverviewOptions(array $options, EntityInterface $entity, array $defaultOverview): array {
    $options = array_merge([
      EntityViewBuilderInterface::FORMAT => TRUE,
      EntityViewBuilderInterface::PROPERTIES => [],
      EntityViewBuilderInterface::ENTITY_LABEL => FALSE,
    ],
      $options
    );

    if (empty($options[EntityViewBuilderInterface::PROPERTIES])) {
      $options[EntityViewBuilderInterface::PROPERTIES] = !empty($defaultOverview) ? $defaultOverview : $this->getOverviewFallback($entity);
    }

    return $options;
  }

  protected function getOverviewFallback(EntityInterface $entity): array {
    $properties = array_flip($entity->getSchema()->getFirstUniqueProperty());
    return array_map(function($value) {
      return NULL;
    }, $properties);
  }

  public function buildExtendedEntityOverview(EntityInterface $entity, array $options = []): array {
    $defaultOverview = $entity->getSchema()
      ->getExtendedEntityOverviewProperties();
    $options = $this->getDefaultOverviewOptions($options, $entity, $defaultOverview);

    $header = [];
    $data = [];

    foreach ($options[EntityViewBuilderInterface::PROPERTIES] as $property => $title) {
      if (!is_string($property)) {
        continue;
      }

      $pathData = $this->entityManager->getItemsFromPath($property, $entity);
      $item = reset($pathData);
      if (!($item instanceof ItemInterface)) {
        continue;
      }
      $itemConfiguration = $item->getConfiguration();
      $header[$property] = empty($title) ? $itemConfiguration->getLabel() : $title;

      $handler = $this->preRenderingItemHandlerLocator->getPreRenderingHandlerFromItem($itemConfiguration);
      $itemData = $handler->getExtendedOverview($item, $options);

      $data[$property] = $itemData;
    }

    $header['validationData'] = 'Logs';
    $data['validationData'] = [
      'type' => EntityViewBuilderInterface::EXT_OVERVIEW_VALIDATION,
      'data' => [
        'red' => $entity->countRedLogs(),
        'yellow' => $entity->countYellowLogs(),
      ],
    ];

    return [
      'header' => $header,
      'data' => $data,
    ];
  }

}
