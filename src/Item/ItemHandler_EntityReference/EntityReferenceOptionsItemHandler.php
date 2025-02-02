<?php

namespace App\Item\ItemHandler_EntityReference;

use App\DataCollections\EntityKeyCollection;
use App\DaViEntity\DaViEntityManager;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityKey;
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
    EntityTypeSchemaRegister $schemaRegister,
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
				$entityKey = $this->buildEntityKey($value, $itemConfiguration, $entity->getClient());
				yield $entityKey;
			}
		}
	}

	public function getLabelFromValue(ItemConfigurationInterface $itemConfiguration, $value, string $client): string {
		$options = $itemConfiguration->getSetting('options', []);

		if(array_key_exists($value, $options)) {
			return $options[$value]['label'] ?? $value;
		}

		$entityKey = $this->buildEntityKey($value, $itemConfiguration, $client);
		return $this->entityManager->getEntityLabel($entityKey) ?? '';
	}

  public function buildEntityKeyCollection(EntityInterface $entity, string $property): EntityKeyCollection | null {
    $collection = new EntityKeyCollection();
    $item = $entity->getPropertyItem($property);
    $values = $item->getValuesAsArray();

    $itemConfiguration = $item->getConfiguration();
    $options = $itemConfiguration->getSetting('options', []);

    foreach ($values as $value) {
      if(in_array($value, $options) || !$this->validateReferenceValue($entity, $property, $value)) {
        $collection->addRawValue($value);
        continue;
      }

      if(is_scalar($value)) {
        $entityKey = $this->buildEntityKey($value, $item->getConfiguration(), $entity->getClient());
        if(!($entityKey instanceof EntityKey)) {
          continue;
        }
        $collection->addKey($entityKey, $value);
      }
    }

    return $collection->hasValues() ? $collection : null;
  }

}
