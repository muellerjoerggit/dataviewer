<?php

namespace App\Item\ItemHandler_Validator;

use App\DaViEntity\DaViEntityManager;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityKey;
use App\Item\ItemConfigurationInterface;
use App\Item\ItemHandler_EntityReference\EntityReferenceItemHandlerLocator;
use App\Item\ItemHandler_Validator\Attribute\ReferenceValidatorItemHandlerDefinition;
use App\Item\ItemInterface;
use App\Logger\Logger;
use App\Services\Validation\ErrorCodes;

/**
 *
 * validates referenced entities
 *
 */
class EntityReferenceValidatorItemHandler extends AbstractValidatorItemHandler implements ValidatorItemHandlerInterface {

  public function __construct(
    private readonly DaViEntityManager $entityManager,
    ErrorCodes $errorCodes,
    Logger $logger
  ) {
    parent::__construct($logger, $errorCodes);
  }

  public function validateItemFromGivenEntity(EntityInterface $entity, string $property): void {
    if ($entity->hasPropertyItem($property)) {
      $item = $entity->getPropertyItem($property);
    } else {
      return;
    }

    $referencedEntities = [];

    foreach ($item->iterateEntityKeyCollection() as $entityKey) {
      if (!$entityKey instanceof EntityKey) {
        continue;
      }

      $referencedEntity = $this->entityManager->getEntity($entityKey);
      $referencedEntities[] = $referencedEntity;
    }

    $this->validateReferencedEntities($referencedEntities, $item->getConfiguration(), $entity);
  }

  private function validateReferencedEntities(array $referencedEntities, ItemConfigurationInterface $itemConfiguration, EntityInterface | null $sourceEntity = null): void {
    $result = true;

    foreach ($itemConfiguration->iterateValidatorItemHandlerDefinitionsByClass(static::class) as $definition) {
      if(!$definition instanceof ReferenceValidatorItemHandlerDefinition) {
        continue;
      }

      $hasAtLeastOneReference = false;
      foreach ($referencedEntities as $referencedEntity) {
        if (!($referencedEntity instanceof EntityInterface)) {
          continue;
        }

        $hasAtLeastOneReference = true;
        $missing = $referencedEntity->isMissingEntity();
        $available = $referencedEntity->isAvailable();
        $critical = $referencedEntity->hasCriticalLogs();

        if ($definition->isNotMissing() && $missing) {
          $this->setItemValidationResultByCode($sourceEntity, $itemConfiguration->getItemName(), $definition->getNotMissingLogCode());
          $result = false;
          break;
        }

        if ($definition->isNotAvailable() && !$available) {
          $this->setItemValidationResultByCode($sourceEntity, $itemConfiguration->getItemName(), $definition->getNotAvailableLogCode());
          $result = false;
          break;
        }

        if ($definition->isNotCritical() && $critical) {
          $this->setItemValidationResultByCode($sourceEntity, $itemConfiguration->getItemName(), $definition->getNotCriticalLogCode());
          $result = false;
          break;
        }
      }

      if(!$definition->isMandatory() && !$hasAtLeastOneReference) {
        $this->setItemValidationResultByCode($sourceEntity, $itemConfiguration->getItemName(), $definition->getMandatoryLogCode());
        $result = false;
      }

      if(!$result) {
        return;
      }
    }
  }

  public function validateValueFromItemConfiguration(ItemConfigurationInterface $itemConfiguration, $value, string $client): bool {
    return false;
  }

}
