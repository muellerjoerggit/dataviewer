<?php

namespace App\EntityServices\OverviewBuilder;

use App\DaViEntity\DaViEntityManager;
use App\DaViEntity\EntityInterface;
use App\EntityServices\ViewBuilder\ViewBuilderInterface;
use App\Item\ItemHandler_Formatter\FormatterItemHandlerLocator;
use App\Item\ItemHandler_PreRendering\PreRenderingItemHandlerLocator;
use App\Item\ItemInterface;
use App\Services\ClientService;

class CommonOverviewBuilder implements OverviewBuilderInterface {

  public function __construct(
    private readonly PreRenderingItemHandlerLocator $preRenderingItemHandlerLocator,
    private readonly DaViEntityManager $entityManager,
    private readonly FormatterItemHandlerLocator $valueFormatterItemHandlerLocator,
    private readonly ClientService $clientService,
  ) {}

  public function buildEntityOverview(EntityInterface $entity, array $options = []): array {
    $defaultOverview = $entity->getSchema()->getEntityOverviewProperties();
    $options = $this->getDefaultOverviewOptions($options, $entity, $defaultOverview);
    $version = $this->clientService->getClientVersion($entity->getClient());

    $header = [];
    $data = [];

    foreach ($options[ViewBuilderInterface::PROPERTIES] as $property => $title) {
      $items = $this->entityManager->getItemsFromPath($property, $entity);
      $item = reset($items);
      if (!($item instanceof ItemInterface)) {
        continue;
      }
      $itemConfiguration = $item->getConfiguration();

      if(!$itemConfiguration->hasVersion($version)) {
        continue;
      }

      $header[$property] = empty($title) ? $itemConfiguration->getLabel() : $title;
      $firstValue = $item->getFirstValueAsString();
      if ($options[ViewBuilderInterface::FORMAT] && $itemConfiguration->hasFormatterHandler()) {
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
      ViewBuilderInterface::FORMAT => TRUE,
      ViewBuilderInterface::PROPERTIES => [],
      ViewBuilderInterface::ENTITY_LABEL => FALSE,
    ],
      $options
    );

    if (empty($options[ViewBuilderInterface::PROPERTIES])) {
      $options[ViewBuilderInterface::PROPERTIES] = !empty($defaultOverview) ? $defaultOverview : $this->getOverviewFallback($entity);
    }

    return $options;
  }

  protected function getOverviewFallback(EntityInterface $entity): array {
    $properties = array_flip($entity->getSchema()->getFirstUniqueProperties());
    return array_map(function($value) {
      return NULL;
    }, $properties);
  }

  public function buildExtendedEntityOverview(EntityInterface $entity, array $options = []): array {
    $defaultOverview = $entity->getSchema()->getExtendedEntityOverviewProperties();
    $options = $this->getDefaultOverviewOptions($options, $entity, $defaultOverview);

    $header = [];
    $data = [];

    foreach ($options[ViewBuilderInterface::PROPERTIES] as $property => $title) {
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
      'type' => ExtEntityOverviewTypes::VALIDATION,
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