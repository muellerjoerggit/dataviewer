<?php

namespace App\Item\ItemHandler_Validator;

use App\DaViEntity\DaViEntityManager;
use App\DaViEntity\EntityInterface;
use App\Item\ItemConfigurationInterface;
use App\Item\ItemHandler_EntityReference\EntityReferenceItemHandlerLocator;
use App\Item\ItemInterface;
use App\Logger\Logger;
use App\Services\Validation\ErrorCodes;

/**
 *
 * ToDo: finish validation
 *
 * validates referenced entities
 *
 * yaml config example:
 * <code>
 * EntityReferenceValidatorItemHandler:
 *  all:
 *    mandatory: true
 *    checks:
 *      - "availability"
 *      - "critical"
 *      - "missing"
 *    logCode: "INT-2000"
 * </code>
 *
 * options:
 * <code>
 *  missing = checks whether the referenced entity exists
 *  availability = checks whether the referenced is available
 *  critical = checks whether the referenced has a critical error
 *  all = all of the above
 * </code>
 */
class EntityReferenceValidatorItemHandler extends AbstractValidatorItemHandler implements ValidatorItemHandlerInterface {

  public const VALIDATION_TYPE_MISSING = 'missing';

  public const VALIDATION_TYPE_AVAILABILITY = 'availability';

  public const VALIDATION_TYPE_ALL = 'all';

  public const VALIDATION_TYPE_CRITICAL = 'critical';

  public function __construct(
    private readonly EntityReferenceItemHandlerLocator $referenceItemHandlerLocator,
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

    $referenceHandler = $this->referenceItemHandlerLocator->getEntityReferenceHandlerFromItem($item->getConfiguration());
    $referencedEntities = [];

    foreach ($item->iterateEntityKeys() as $entityKey) {
      if (!$entityKey) {
        continue;
      }

      $referencedEntity = $this->entityManager->getEntity($entityKey);
      //			$this->entityManager->validateEntity($referencedEntity);
      $referencedEntities[] = $referencedEntity;
    }

    $this->validateReferencedEntities($referencedEntities, $item, $entity);
  }

  private function validateReferencedEntities(array $referencedEntities, ItemInterface|ItemConfigurationInterface $item, ?EntityInterface $sourceEntity = NULL): array {
    if ($item instanceof ItemInterface) {
      $itemConfiguration = $item->getConfiguration();
    } elseif ($item instanceof ItemConfigurationInterface) {
      $itemConfiguration = $item;
      $item = NULL;
    }
    $handlerSettings = $itemConfiguration->getValidatorItemHandlerSettings($this::class);
    $label = $itemConfiguration->getLabel();
    $totalResult = [];

    foreach ($handlerSettings as $validationSetting) {
      $mandatory = $validationSetting['mandatory'] ?? FALSE;
      $resultHandlerSetting = [];
      $checks = $validationSetting['checks'] ?? [];
      $logCode = $validationSetting['logCode'] ?? ErrorCodes::ERROR_CODE_MISSING;

      if (empty($checks)) {
        continue;
      }

      if (!is_array($checks)) {
        $checks = [$checks];
      }

      foreach ($referencedEntities as $referencedEntity) {
        if (!($referencedEntity instanceof EntityInterface)) {
          continue;
        }

        $result = TRUE;

        if (
          in_array(self::VALIDATION_TYPE_MISSING, $checks)
          && ($referencedEntity->isMissingEntity())
        ) {
          $result = FALSE;
          //				} elseif (
          //					in_array(self::VALIDATION_TYPE_AVAILABILITY, $checks)
          //					&& (!$referencedEntity->isAvailable())
          //				) {
          //					$result = false;
        } elseif (
          in_array(self::VALIDATION_TYPE_CRITICAL, $checks)
          && ($referencedEntity->hasCriticalLogs())
        ) {
          $result = FALSE;
        }

        if (!$result) {
          $this->setItemValidationResultByCode($sourceEntity, $itemConfiguration->getItemName(), $logCode);
          $resultHandlerSetting[] = FALSE;
        } else {
          $resultHandlerSetting[] = TRUE;
        }
      }

      if ($mandatory && empty($resultHandlerSetting)) {
        $this->setItemValidationResultByCode($sourceEntity, $itemConfiguration->getItemName(), $logCode);
        $resultHandlerSetting[] = FALSE;
      }

      $totalResult[] = in_array(FALSE, $resultHandlerSetting);
    }

    return $totalResult;
  }

  public function validateValueFromItemConfiguration(ItemConfigurationInterface $itemConfiguration, $value, string $client): bool {
    $referenceHandler = $this->referenceItemHandlerLocator->getEntityReferenceHandlerFromItem($itemConfiguration);
    $entityKey = $referenceHandler->buildEntityKey($value, $itemConfiguration, $client);
    $referencedEntity = $this->entityManager->getEntity($entityKey);
    //		$this->entityManager->validateEntity($referencedEntity);

    $totalResult = $this->validateReferencedEntities([$referencedEntity], $itemConfiguration);

    return in_array(FALSE, $totalResult);
  }

}
