<?php

namespace App\Item\ItemHandler_EntityReference;

use App\Database\DaViQueryBuilder;
use App\Database\TableJoinBuilder;
use App\DataCollections\EntityKeyCollection;
use App\DaViEntity\DaViEntityManager;
use App\DaViEntity\EntityKey;
use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\DaViEntity\Schema\EntityTypesRegister;
use App\DaViEntity\UniqueKey;
use App\EntityTypes\NullEntity\NullEntity;
use App\Item\ItemConfigurationInterface;
use App\Item\ItemHandler_EntityReference\Attribute\EntityReferenceItemHandlerDefinition;
use App\Item\ItemHandler_EntityReference\Attribute\EntityReferenceItemHandlerDefinitionInterface;
use App\Item\ItemHandler_Validator\ValidatorItemHandlerInterface;
use App\Item\ItemHandler_Validator\ValidatorItemHandlerLocator;
use App\DaViEntity\EntityInterface;

class CommonEntityReferenceItemHandler implements SimpleEntityReferenceJoinInterface {

	public function __construct(
		protected readonly DaViEntityManager $entityManager,
		protected readonly ValidatorItemHandlerLocator $validatorHandlerLocator,
		protected readonly EntityTypeSchemaRegister $schemaRegister,
    protected readonly EntityTypesRegister $entityTypesRegister,
    protected readonly TableJoinBuilder $joinBuilder,
	) {}

  protected function isDefinitionValid($referenceDefinition): bool {
    return $referenceDefinition instanceof EntityReferenceItemHandlerDefinition;
  }

  protected function validateReferenceValue(ItemConfigurationInterface $targetItemConfiguration, $value, string $client): bool {
    $validatorHandlers = $this->validatorHandlerLocator->getValidatorHandlerFromItem($targetItemConfiguration);

    foreach ($validatorHandlers as $validationHandler) {
      if(!($validationHandler instanceof ValidatorItemHandlerInterface)) {
        continue;
      }

      if(!$validationHandler->validateValueFromItemConfiguration($targetItemConfiguration, $value, $client)) {
        return false;
      }
    }

    return true;
  }

	public function buildEntityKey($value, EntityReferenceItemHandlerDefinitionInterface $referenceDefinition, string $client): ?EntityKey	{
    if(!$this->isDefinitionValid($referenceDefinition)) {
      return null;
    }

    [$entityClass, $property] = $this->getTargetSetting($referenceDefinition);
    $entityType = $this->entityTypesRegister->getEntityTypeByEntityClass($entityClass);

		if(empty($entityType) || empty($property) || !is_scalar($value)) {
			return null;
		}

		$identifiers = (new UniqueKey())->addIdentifier($property, $value);

		return EntityKey::create($client, $entityType, [$identifiers]);
	}

	public function getEntityLabel($entityKey): string {
		return $this->entityManager->getEntityLabel($entityKey) ?? '';
	}

	public function getEntityOverview($entityKey, array $options = []): array {
		return $this->entityManager->getEntityOverview($entityKey, $options) ?? [];
	}

  public function getTargetSetting(EntityReferenceItemHandlerDefinitionInterface | ItemConfigurationInterface $referenceDefinition): array {
    if($referenceDefinition instanceof ItemConfigurationInterface) {
      $referenceDefinition = $this->getReferenceDefinition($referenceDefinition);
    }

    if(!$referenceDefinition || !$this->isDefinitionValid($referenceDefinition)) {
      return [NullEntity::class, 'id'];
    }

    return [$referenceDefinition->getTargetEntity(), $referenceDefinition->getTargetProperty()];
  }

	protected function getTargetItemConfiguration(EntityReferenceItemHandlerDefinitionInterface $referenceDefinition): ItemConfigurationInterface {
    [$entityClass, $property] = $this->getTargetSetting($referenceDefinition);
		$schema = $this->schemaRegister->getSchemaFromEntityClass($entityClass);
		return $schema->getProperty($property);
	}

  public function getLabelFromValue(ItemConfigurationInterface $itemConfiguration, $value, string $client): string {
    $referenceDefinition = $itemConfiguration->getReferenceItemHandlerDefinition();
    if(!$this->isDefinitionValid($referenceDefinition)) {
      return '';
    }

    $entityKey = $this->buildEntityKey($value, $referenceDefinition, $client);
    return $this->entityManager->getEntityLabel($entityKey) ?? '';
  }

  protected function getReferenceDefinition(ItemConfigurationInterface $itemConfiguration): EntityReferenceItemHandlerDefinitionInterface | null {
    if($itemConfiguration->hasEntityReferenceHandler() && $this->isDefinitionValid($itemConfiguration->getReferenceItemHandlerDefinition())) {
      return $itemConfiguration->getReferenceItemHandlerDefinition();
    }

    return null;
  }

  public function buildEntityKeyCollection(EntityInterface $entity, string $property): EntityKeyCollection | null {
    $collection = new EntityKeyCollection();
    $item = $entity->getPropertyItem($property);
    $values = $item->getValuesAsArray();
    $itemConfiguration = $item->getConfiguration();
    $referenceDefinition = $this->getReferenceDefinition($itemConfiguration);
    $client = $entity->getClient();

    if($referenceDefinition) {
      $targetItemConfiguration = $this->getTargetItemConfiguration($referenceDefinition);
    }

    foreach ($values as $value) {
      if(!$referenceDefinition || !$this->validateReferenceValue($targetItemConfiguration, $value, $client)) {
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

  public function joinTable(DaViQueryBuilder $queryBuilder, ItemConfigurationInterface $itemConfiguration, EntitySchema $fromSchema, bool $innerJoin = false): void {
    [$targetClass, $targetProperty] = $this->getTargetSetting($itemConfiguration);
    $toSchema = $this->schemaRegister->getSchemaFromEntityClass($targetClass);
    $fromProperty = $itemConfiguration->getItemName();

    $this->joinBuilder->joinTable($queryBuilder, $fromSchema, $fromProperty, $toSchema, $targetProperty, $innerJoin);
  }

}
