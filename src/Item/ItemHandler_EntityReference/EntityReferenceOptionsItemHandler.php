<?php

namespace App\Item\ItemHandler_EntityReference;

use App\DataCollections\EntityKeyCollection;
use App\DaViEntity\DaViEntityManager;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityKey;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\DaViEntity\Schema\EntityTypesRegister;
use App\Item\ItemConfigurationInterface;
use App\Item\ItemHandler_Validator\ValidatorItemHandlerLocator;
use App\Item\Property\Attribute\OptionItemSettingDefinition;

/**
 * Item handler for entity references with few preconfigured non-reference exceptions
 */
class EntityReferenceOptionsItemHandler extends CommonEntityReferenceItemHandler {

	public function __construct(
    DaViEntityManager $entityManager,
    ValidatorItemHandlerLocator $validatorHandlerLocator,
    EntityTypeSchemaRegister $schemaRegister,
    EntityTypesRegister $entityTypesRegister
  ) {
		parent::__construct($entityManager, $validatorHandlerLocator, $schemaRegister, $entityTypesRegister);
	}

	public function getLabelFromValue(ItemConfigurationInterface $itemConfiguration, $value, string $client): string {
		$options = $this->getOptionSetting($itemConfiguration);

		if($options instanceof OptionItemSettingDefinition) {
			return $options->getLabel($value);
		}

    $referenceDefinition = $this->getReferenceDefinition($itemConfiguration);
    if($referenceDefinition) {
      $entityKey = $this->buildEntityKey($value, $referenceDefinition, $client);
      $this->entityManager->getEntityLabel($entityKey);
    }

		return '';
	}

  private function getOptionSetting(ItemConfigurationInterface $itemConfiguration): OptionItemSettingDefinition | null {
    if($itemConfiguration->hasSetting(OptionItemSettingDefinition::class)) {
      return $itemConfiguration->getSetting(OptionItemSettingDefinition::class);
    }

    return null;
  }

  public function buildEntityKeyCollection(EntityInterface $entity, string $property): EntityKeyCollection | null {
    $collection = new EntityKeyCollection();
    $item = $entity->getPropertyItem($property);
    $values = $item->getValuesAsArray();
    $itemConfiguration = $item->getConfiguration();
    $options = $this->getOptionSetting($itemConfiguration);
    $referenceDefinition = $this->getReferenceDefinition($itemConfiguration);
    $client = $entity->getClient();
    $hasValidator = false;

    if($referenceDefinition) {
      $targetItemConfiguration = $this->getTargetItemConfiguration($referenceDefinition);
      $hasValidator = $targetItemConfiguration->hasValidatorHandlerDefinition();
    }

    foreach ($values as $value) {
      if(
        ($options instanceof OptionItemSettingDefinition && $options->hasOption($value) )
        || !$referenceDefinition
        || !$hasValidator
        || !$this->validateReferenceValue($targetItemConfiguration, $value, $client)
      ) {
        $collection->addRawValue($value);
        continue;
      }

      $entityKey = $this->buildEntityKey($value, $referenceDefinition, $entity->getClient());
      if(!($entityKey instanceof EntityKey)) {
        $collection->addRawValue($value);
        continue;
      }
      $collection->addKey($entityKey, $value);
    }

    return $collection->hasValues() ? $collection : null;
  }

}
