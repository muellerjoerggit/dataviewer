<?php

namespace App\Item\ItemHandler_EntityReference;

use App\Database\TableReferenceHandler\Attribute\CommonTableReferenceAttr;
use App\Database\TableReferenceHandler\CommonTableReferenceHandler;
use App\DataCollections\EntityKeyCollection;
use App\DaViEntity\DaViEntityManager;
use App\DaViEntity\EntityKey;
use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\DaViEntity\UniqueIdentifier;
use App\Item\ItemConfigurationInterface;
use App\Item\ItemHandler_Validator\ValidatorItemHandlerInterface;
use App\Item\ItemHandler_Validator\ValidatorItemHandlerLocator;
use App\DaViEntity\EntityInterface;
use App\Database\TableReferenceHandler\Attribute\TableReferenceAttrInterface;

class CommonEntityReferenceItemHandler implements EntityReferenceItemHandlerInterface {

	public function __construct(
		protected readonly DaViEntityManager $entityManager,
		protected readonly ValidatorItemHandlerLocator $validatorHandlerLocator,
		protected readonly EntityTypeSchemaRegister $schemaRegister,
	) {}

  protected function validateReferenceValue(EntityInterface $entity, string $property, $value): bool {
    $targetItemConfiguration = $this->getTargetItemConfiguration($entity->getPropertyItem($property)->getConfiguration());
    $validatorHandlers = $this->validatorHandlerLocator->getValidatorHandlerFromItem($targetItemConfiguration);
    $client = $entity->getClient();

    foreach ($validatorHandlers as $validationHandler) {
      if(!($validationHandler instanceof ValidatorItemHandlerInterface)) {
        continue;
      }
      $validationResult = $validationHandler->validateValueFromItemConfiguration($targetItemConfiguration, $value, $client);

      if(!$validationResult) {
        return false;
      }
    }

    return true;
  }

	public function buildEntityKey($value, ItemConfigurationInterface $itemConfiguration, string $client): ?EntityKey	{
    [$entityType, $property] = $this->getTargetSetting($itemConfiguration);

		if(empty($entityType) || empty($property)) {
			return null;
		}

		$identifiers = (new UniqueIdentifier())->addIdentifier($property, $value);

		return EntityKey::create($client, $entityType, [$identifiers]);
	}

	public function getEntityLabel($entityKey): string {
		return $this->entityManager->getEntityLabel($entityKey) ?? '';
	}

	public function getEntityOverview($entityKey, array $options = []): array {
		return $this->entityManager->getEntityOverview($entityKey, $options) ?? [];
	}

  public function getTargetSetting(ItemConfigurationInterface $itemConfiguration): array {
    $referenceSettings = $itemConfiguration->getEntityReferenceHandlerSetting();
    $entityType = $referenceSettings[EntityReferenceItemHandlerInterface::YAML_PARAM_TARGET_ENTITY_TYPE] ?? '';
    $property = $referenceSettings['target_property'] ?? '';

    return [$entityType, $property];
  }

	protected function getTargetItemConfiguration(ItemConfigurationInterface $itemConfiguration): ItemConfigurationInterface {
		$referenceSettings = $itemConfiguration->getEntityReferenceHandlerSetting();
		$entityType = $referenceSettings[EntityReferenceItemHandlerInterface::YAML_PARAM_TARGET_ENTITY_TYPE] ?? '';
		$property = $this->getTargetProperty($itemConfiguration);
		$schema = $this->schemaRegister->getEntityTypeSchema($entityType);
		return $schema->getProperty($property);
	}

  public function getTargetProperty(ItemConfigurationInterface $itemConfiguration): string {
    $referenceSettings = $itemConfiguration->getEntityReferenceHandlerSetting();
    return $referenceSettings['target_property'] ?? '';
  }

  public function getLabelFromValue(ItemConfigurationInterface $itemConfiguration, $value, string $client): string {
    $entityKey = $this->buildEntityKey($value, $itemConfiguration, $client);
    return $this->entityManager->getEntityLabel($entityKey) ?? '';
  }

  public function getTargetEntityType(ItemConfigurationInterface $itemConfiguration): string {
    $referenceSettings = $itemConfiguration->getEntityReferenceHandlerSetting();
    return $referenceSettings[EntityReferenceItemHandlerInterface::YAML_PARAM_TARGET_ENTITY_TYPE] ?? '';
  }

  public function buildTableReferenceConfiguration(ItemConfigurationInterface $itemConfiguration, EntitySchema $schema): TableReferenceAttrInterface {
    $property = $itemConfiguration->getItemName();
    $key = 'ref_' . $property;

    $attr = CommonTableReferenceAttr::create($key, CommonTableReferenceHandler::class, $this->getTargetEntityType($itemConfiguration), [$property => $this->getTargetProperty($itemConfiguration)]);

    $attr
      ->setExternalName($key)
      ->setFromEntityClass($schema->getEntityClass());

    return $attr;
  }

  public function buildEntityKeyCollection(EntityInterface $entity, string $property): EntityKeyCollection | null {
    $collection = new EntityKeyCollection();
    $item = $entity->getPropertyItem($property);
    $values = $item->getValuesAsArray();

    foreach ($values as $value) {
      if(!$this->validateReferenceValue($entity, $property, $value)) {
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
