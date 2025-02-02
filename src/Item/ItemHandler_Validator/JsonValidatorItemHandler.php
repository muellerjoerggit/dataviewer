<?php

namespace App\Item\ItemHandler_Validator;

use App\DaViEntity\EntityInterface;
use App\Item\ItemConfigurationInterface;
use App\Services\Validation\ErrorCodes;

/**
 * validates json
 *
 * yaml config example:
 * <code>
 * JsonValidatorItemHandler:
 *   jsonType: "jsonObject"
 *   jsonMandatory: true
 *   logCode: "INT-2000"
 * </code>
 *
 * options:
 * <code>
 *  jsonType = mandatory json type
 *  jsonMandatory = if false then null and empty strings are skipped
 * respectively are valid; default false
 * </code>
 */
class JsonValidatorItemHandler extends AbstractValidatorItemHandler {

  public const JSON_TYPE_OBJECT = 'jsonObject';

  /** array of json objects */
  public const JSON_ARRAY_OBJECTS = 'arrayObjects';

  public function validateItemFromGivenEntity(EntityInterface $entity, string $property): void {
    [
      $item,
      $itemConfiguration,
      $handlerSetting,
    ] = $this->getContext($entity, $property);

    if (!$item) {
      return;
    }

    $errorCode = $handlerSetting['logCode'] ?? ErrorCodes::ERROR_CODE_MISSING;

    foreach ($item->iterateValues() as $value) {
      if (!$this->validateJson($value, $itemConfiguration)) {
        $this->setItemValidationResultByCode($entity, $property, $errorCode);
      }
    }
  }

  private function validateJson(mixed $value, ItemConfigurationInterface $itemConfiguration): bool {
    $handlerSetting = $itemConfiguration->getValidatorItemHandlerSettings(static::class);
    $jsonType = $handlerSetting['jsonType'] ?? '';
    $jsonMandatory = $handlerSetting['jsonMandatory'] ?? FALSE;

    if (!$jsonMandatory && ($value === NULL || $value === '')) {
      return TRUE;
    }

    if ($jsonType === self::JSON_TYPE_OBJECT && !$this->isValidJsonObject($value)) {
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
    if (!$this->validateJson($value, $itemConfiguration)) {
      return FALSE;
    }

    return TRUE;
  }

}
