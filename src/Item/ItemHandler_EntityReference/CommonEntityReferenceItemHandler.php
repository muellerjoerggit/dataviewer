<?php

namespace App\Item\ItemHandler_EntityReference;

use App\DaViEntity\DaViEntityManager;
use App\DaViEntity\EntityKey;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\Item\ItemConfigurationInterface;
use App\Item\ItemHandler_Validator\ValidatorItemHandlerInterface;
use App\Item\ItemHandler_Validator\ValidatorItemHandlerLocator;
use App\DaViEntity\EntityInterface;

class CommonEntityReferenceItemHandler implements EntityReferenceItemHandlerInterface {

	public function __construct(
		protected readonly DaViEntityManager $entityManager,
		protected readonly ValidatorItemHandlerLocator $validatorHandlerLocator,
		protected readonly EntityTypeSchemaRegister $schemaRegister
	) {}

	public function getEntityKeys(EntityInterface $entity, string $property): array {
		$ret = [];
		foreach ($this->iterateEntityKeys($entity, $property) as $entityKey) {
			$ret[] = $entityKey;
		}
		return $ret;
	}

	public function iterateEntityKeys(EntityInterface $entity, string $property): \Generator {
		$item = $entity->getPropertyItem($property);

		if($item->hasEntityKeys()) {
			$iteratorData = $item->iterateEntityKeys();
		} else {
			$iteratorData = $item->getValuesAsOneDimensionalArray();
		}

		foreach ($iteratorData as $value) {
			if($value instanceof EntityKey) {
				yield $value;
				continue;
			}

			if(!$this->validateReferenceValue($entity, $property, $value)) {
				continue;
			}

			if(is_scalar($value)) {
				$entityKey = $this->buildEntityKeys($value, $item->getConfiguration(), $entity->getClient());
				if(!($entityKey instanceof EntityKey)) {
					continue;
				}
				$item->addEntityKey($entityKey);
				yield $entityKey;
			}
		}
	}

  protected function validateReferenceValue(EntityInterface $entity, string $property, $value): bool {
    $targetItemConfiguration = $this->getTargetItemConfiguration($entity->getPropertyItem($property)->getConfiguration());
    $validatorHandlers = $this->validatorHandlerLocator->getValidatorHandlerFromItem($targetItemConfiguration);
    $client = $entity->getClient();

    foreach ($validatorHandlers as $validationHandler) {
      if(!($validationHandler instanceof ValidatorItemHandlerInterface)) {
        continue;
      }
      $validationResult = $validationHandler->validateValueFromItemConfiguration($targetItemConfiguration, $value, $client);

      // $validationHandler->reset();
      if(!$validationResult) {
        return false;
      }
    }

    return true;
  }

	public function buildEntityKeys($value, ItemConfigurationInterface $itemConfiguration, string $client): ?EntityKey	{
    [$entityType, $property] = $this->getTargetSetting($itemConfiguration);

		if(empty($entityType) || empty($property)) {
			return null;
		}

		$identifiers = [[$property => $value]];

		return EntityKey::create($client, $entityType, $identifiers);
	}

	public function getEntityLabel($entityKey): string {
		return $this->entityManager->getEntityLabel($entityKey) ?? '';
	}

	public function getEntityOverview($entityKey, array $options = []): array {
		return $this->entityManager->getEntityOverview($entityKey, $options) ?? [];
	}

  public function getTargetSetting(ItemConfigurationInterface $itemConfiguration): array {
    $referenceSettings = $itemConfiguration->getEntityReferenceHandlerSetting();
    $entityType = $referenceSettings['target_entity_type'] ?? '';
    $property = $referenceSettings['target_property'] ?? '';

    return [$entityType, $property];
  }

	protected function getTargetItemConfiguration(ItemConfigurationInterface $itemConfiguration): ItemConfigurationInterface {
		$referenceSettings = $itemConfiguration->getEntityReferenceHandlerSetting();
		$entityType = $referenceSettings['target_entity_type'] ?? '';
		$property = $referenceSettings['target_property'] ?? '';
		$schema = $this->schemaRegister->getEntityTypeSchema($entityType);
		return $schema->getProperty($property);
	}

  public function getLabelFromValue(ItemConfigurationInterface $itemConfiguration, $value, string $client): string {
    $entityKey = $this->buildEntityKeys($value, $itemConfiguration, $client);
    return $this->entityManager->getEntityLabel($entityKey) ?? '';
  }
}
