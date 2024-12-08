<?php

namespace App\Item\ItemHandler_PreRendering;

use App\DaViEntity\DaViEntityManager;
use App\DaViEntity\EntityKey;
use App\DaViEntity\EntityViewBuilderInterface;
use App\Item\ItemConfigurationInterface;
use App\Item\ItemHandler_EntityReference\EntityReferenceItemHandlerLocator;
use App\Item\ItemHandler_ValueFormatter\ValueFormatterItemHandlerLocator;
use App\DaViEntity\EntityInterface;
use App\Item\ItemInterface;
use App\Item\ReferenceItemInterface;

class EntityReferencePreRenderingItemHandler extends AbstractPreRenderingItemHandler  {

	public function __construct(
    protected readonly EntityReferenceItemHandlerLocator $referenceHandlerLocator,
    protected readonly DaViEntityManager $entityManager,
    ValueFormatterItemHandlerLocator $formatterLocator,
  ) {
		parent::__construct($formatterLocator);
	}

  protected function getEntities(EntityInterface $entity, string $property): array {
    $item = $entity->getPropertyItem($property);
    $entities = [];
    if(!$item instanceof ReferenceItemInterface && $item->countValues() === 0) {
      return [];
    } elseif(!$item instanceof ReferenceItemInterface && $item->countValues() > 0) {
      foreach ($item->iterateValues() as $value) {
        $entities[] = $this->buildValueArray($value);
      }
    } else {
      $itemConfiguration = $item->getConfiguration();
      $options = $this->getEntityOverviewOptions($itemConfiguration);

      foreach ($item->iterateEntityKeys() as $entityKey) {
        $entities[] = $this->buildEntityArray($entityKey, $options);
      }
    }

    return $entities;
  }

  protected function getEntityOverviewOptions(ItemConfigurationInterface $itemConfiguration): array {
    $config = $itemConfiguration->getPreRenderingHandlerSetting();
    return [
      EntityViewBuilderInterface::FORMAT => false,
      EntityViewBuilderInterface::PROPERTIES => $config['entityOverview'] ?? [],
    ];
  }

  protected function buildEntityArray(EntityKey $entityKey, array $options): array {
    $entity = $this->entityManager->getEntity($entityKey);
    $labelEntity = $this->entityManager->getEntityLabel($entity);
    $entityOverview = $this->entityManager->getEntityOverview($entity, $options);
    $entityKeyString = $entity->getFirstEntityKeyAsString();

    return [
      'label' => $labelEntity,
      'entityKey' => $entityKeyString,
      'entityOverview' => $entityOverview
    ];
  }

  protected function buildValueArray($value): array {
    return [
      'label' => $value,
      'entityKey' => '',
      'entityOverview' => []
    ];
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
				'warningError' => $item->isYellowError()
			]
		];

		if($item->getConfiguration()->isCardinalityMultiple()) {
			$ret['component'] = 'EntityOverviewItem';
		}


		return $ret;
	}

  public function getExtendedOverview(ItemInterface $item, array $options): array {
    if(!$item instanceof ReferenceItemInterface || !$item->hasEntityKeys()) {
      return parent::getExtendedOverview($item, $options);
    }

    $referenceHandler = $this->referenceHandlerLocator->getEntityReferenceHandlerFromItem($item->getConfiguration());
    $entityKey = $item->getFirstEntityKey();
    $overview = $referenceHandler->getEntityOverview($entityKey);

    return [
      'type' => EntityViewBuilderInterface::EXT_OVERVIEW_REFERENCE,
      'data' => [
        'entityKey' => $entityKey->getFirstEntityKeyAsString(),
        'label' => $options[EntityViewBuilderInterface::ENTITY_LABEL] ? $referenceHandler->getEntityLabel($entityKey) : $entityKey->getFirstUniqueIdentifierAsString(),
        'entityOverview' => $overview
      ]];
  }

}
