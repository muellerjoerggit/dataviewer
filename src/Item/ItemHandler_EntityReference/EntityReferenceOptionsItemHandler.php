<?php

namespace App\Item\ItemHandler_EntityReference;

use App\DaViEntity\DaViEntityManager;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\Item\ItemConfigurationInterface;
use App\Item\ItemHandler_Validator\ValidatorItemHandlerLocator;

/**
 * Item handler for entity references with few preconfigured non-reference exceptions
 */
class EntityReferenceOptionsItemHandler extends CommonEntityReferenceItemHandler {

	public function __construct(
    DaViEntityManager $entityManager,
    ValidatorItemHandlerLocator $validatorHandlerLocator,
    EntityTypeSchemaRegister $schemaRegister
  ) {
		parent::__construct($entityManager, $validatorHandlerLocator, $schemaRegister);
	}

	public function iterateEntityKeys(EntityInterface $entity, string $property): \Generator {
		$item = $entity->getPropertyItem($property);
    $itemConfiguration = $item->getConfiguration();

		$options = $itemConfiguration->getSetting('options', []);
		foreach ($item->getValuesAsOneDimensionalArray() As $value) {
			if(in_array($value, $options)) {
				continue;
			}

			if(!$this->validateReferenceValue($entity, $property, $value)) {
				continue;
			}

			if(!is_array($value)) {
				$entityKey = $this->buildEntityKeys($value, $itemConfiguration, $entity->getClient());
				yield $entityKey;
			}
		}
	}

	public function getTargetEntityTypes(ItemConfigurationInterface $itemConfiguration): array {
		$referenceSettings = $itemConfiguration->getEntityReferenceHandlerSetting();
		$entityType = $referenceSettings['target_entity_type'] ?? '';

		if(!empty($entityType)) {
			return [$entityType];
		} else {
			return [];
		}
	}

	public function getLabelFromValue(ItemConfigurationInterface $itemConfiguration, $value, string $client): string {
		$options = $itemConfiguration->getSetting('options', []);

		if(array_key_exists($value, $options)) {
			return $options[$value]['label'] ?? $value;
		}

		$entityKey = $this->buildEntityKeys($value, $itemConfiguration, $client);
		return $this->entityManager->getEntityLabel($entityKey) ?? '';
	}

}
