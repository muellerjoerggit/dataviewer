<?php

namespace App\Item\ItemHandler_PreRendering;

use App\DaViEntity\DaViEntityManager;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityKey;
use App\EntityServices\OverviewBuilder\ExtEntityOverviewTypes;
use App\EntityServices\ViewBuilder\ViewBuilderInterface;
use App\Item\ItemConfigurationInterface;
use App\Item\ItemHandler_EntityReference\EntityReferenceItemHandlerLocator;
use App\Item\ItemHandler_Formatter\FormatterItemHandlerLocator;
use App\Item\ItemInterface;
use App\Item\ReferenceItemInterface;

class EntityReferencePreRenderingItemHandler extends AbstractPreRenderingItemHandler {

  public function __construct(
    protected readonly EntityReferenceItemHandlerLocator $referenceHandlerLocator,
    protected readonly DaViEntityManager $entityManager,
    FormatterItemHandlerLocator $formatterLocator,
  ) {
    parent::__construct($formatterLocator);
  }

  public function getComponentPreRenderArray(EntityInterface $entity, string $property): array {
    $item = $entity->getPropertyItem($property);
    $entities = $this->getEntities($entity, $property);
    $ret = [
      'component' => 'EntityReferenceItem',
      'name' => $item->getConfiguration()->getItemName(),
      'documentation' => [
        'label' => $item->getConfiguration()->getLabel(),
        'description' => $item->getConfiguration()->getDescription(),
        //				'deprecated' => $item->getConfiguration()->getDeprecated(),
      ],
      'data' => [
        'entities' => $entities,
        'isNull' => $item->isValuesNull() || empty($entities),
        'criticalError' => $item->isRedError(),
        'warningError' => $item->isYellowError(),
      ],
    ];

    if ($item->getConfiguration()->isCardinalityMultiple()) {
      $ret['component'] = 'EntityOverviewItem';
    }

    return $ret;
  }

  protected function getEntities(EntityInterface $entity, string $property): array {
    $item = $entity->getPropertyItem($property);
    $entities = [];
    if(!$item->hasEntityKeys()) {
      return $entities;
    }

    $itemConfiguration = $item->getConfiguration();
    $options = $this->getEntityOverviewOptions($itemConfiguration);
    foreach ($item->iterateEntityKeys() as $entityKey) {
      $entities[] = $this->buildEntityArray($entityKey, $options);
    }

    return $entities;
  }

  protected function buildValueArray($value): array {
    return [
      'label' => $value,
      'entityKey' => '',
      'entityOverview' => [],
    ];
  }

  protected function getEntityOverviewOptions(ItemConfigurationInterface $itemConfiguration): array {
    $config = $itemConfiguration->getPreRenderingHandlerDefinition();
    return [
      ViewBuilderInterface::FORMAT => FALSE,
//      ViewBuilderInterface::PROPERTIES => $config['entityOverview'] ?? [],
      ViewBuilderInterface::PROPERTIES => [],
    ];
  }

  protected function buildEntityArray(EntityKey | null $entityKey, array $options): array {
    if(!$entityKey) {
      return $this->buildValueArray('Fehler EntityKey');
    }
    $entity = $this->entityManager->getEntity($entityKey);
    $labelEntity = $this->entityManager->getEntityLabel($entity);
    $entityOverview = $this->entityManager->getEntityOverview($entity, $options);
    $entityKeyString = $entity->getFirstEntityKeyAsString();

    return [
      'label' => $labelEntity,
      'entityKey' => $entityKeyString,
      'entityOverview' => $entityOverview,
    ];
  }

  public function getExtendedOverview(ItemInterface $item, array $options): array {
    if (!$item instanceof ReferenceItemInterface || !$item->hasEntityKeys()) {
      return parent::getExtendedOverview($item, $options);
    }

    $referenceHandler = $this->referenceHandlerLocator->getEntityReferenceHandlerFromItem($item->getConfiguration());
    $entityKey = $item->getFirstEntityKey();
    $overview = $referenceHandler->getEntityOverview($entityKey);

    return [
      'type' => ExtEntityOverviewTypes::REFERENCE,
      'data' => [
        'entityKey' => $entityKey->getFirstEntityKeyAsString(),
        'label' => $options[ViewBuilderInterface::ENTITY_LABEL] ? $referenceHandler->getEntityLabel($entityKey) : $entityKey->getFirstUniqueIdentifierAsString(),
        'entityOverview' => $overview,
      ],
    ];
  }

}
