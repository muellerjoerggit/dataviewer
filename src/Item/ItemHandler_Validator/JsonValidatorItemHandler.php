<?php

namespace App\Item\ItemHandler_Validator;

use App\DaViEntity\EntityInterface;
use App\Item\ItemConfigurationInterface;
use App\Item\ItemHandler_Validator\Attribute\JsonValidatorItemHandlerDefinition;
use App\Services\Validation\ErrorCodes;

/**
 * validates json
 */
class JsonValidatorItemHandler extends AbstractValidatorItemHandler {

  public function validateItemFromGivenEntity(EntityInterface $entity, string $property): void {
    if ($entity->hasPropertyItem($property)) {
      $item = $entity->getPropertyItem($property);
      $itemConfiguration = $item->getConfiguration();
    } else {
      return;
    }

    foreach ($item->iterateValues() as $value) {
      foreach ($itemConfiguration->iterateValidatorItemHandlerDefinitionsByClass(static::class) as $definition) {
        if(!$definition instanceof JsonValidatorItemHandlerDefinition) {
          continue;
        }

        if (!$this->validateJson($value, $itemConfiguration)) {
          $this->setItemValidationResultByCode($entity, $property, $definition->getLogCode());
        }
      }
    }
  }

  private function validateJson(JsonValidatorItemHandlerDefinition $definition, mixed $value): bool {

    if(!$definition->isJsonMandatory() && ($value === NULL || $value === '')) {
      return true;
    }

    if ($definition->isJsonObject() && !$this->isValidJsonObject($value)) {
      return FALSE;
    }

    if (!json_validate($value)) {
      return FALSE;
    }

    return TRUE;
  }

  private function isValidJsonObject(mixed $value): bool {
    if (!is_string($value)) {
      $value = (string) $value;
    }

    $value = trim($value);

    if (!str_starts_with($value, '{') && !str_ends_with($value, '}')) {
      return FALSE;
    }

    $json = json_decode($value, TRUE);

    if (!$json) {
      return FALSE;
    }

    return TRUE;
  }

  public function validateValueFromItemConfiguration(ItemConfigurationInterface $itemConfiguration, $value, string $client): bool {
    foreach ($itemConfiguration->iterateValidatorItemHandlerDefinitionsByClass(static::class) as $definition) {
      if(!$definition instanceof JsonValidatorItemHandlerDefinition) {
        continue;
      }

      if (!$this->validateJson($value, $itemConfiguration)) {
        return false;
      }
    }

    return TRUE;
  }

}
