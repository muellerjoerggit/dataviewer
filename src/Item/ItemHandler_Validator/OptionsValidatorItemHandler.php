<?php

namespace App\Item\ItemHandler_Validator;

use App\DaViEntity\EntityInterface;
use App\Item\ItemConfigurationInterface;
use App\Item\ItemHandler_Validator\Attribute\ValidatorItemHandlerDefinition;
use App\Item\Property\Attribute\OptionItemSettingDefinition;

class OptionsValidatorItemHandler extends AbstractValidatorItemHandler {

  public function validateItemFromGivenEntity(EntityInterface $entity, string $property): void {
    if ($entity->hasPropertyItem($property)) {
      $item = $entity->getPropertyItem($property);
      $itemConfiguration = $item->getConfiguration();
    } else {
      return;
    }

    $options = null;
    if($itemConfiguration->hasSetting(OptionItemSettingDefinition::class)) {
      $options = $itemConfiguration->getSetting(OptionItemSettingDefinition::class);
    }

    foreach ($item->iterateValues() as $value) {
      foreach ($itemConfiguration->iterateValidatorItemHandlerDefinitionsByClass(static::class) as $definition) {
        if (!$definition instanceof ValidatorItemHandlerDefinition) {
          continue;
        }

        if (!$options instanceof OptionItemSettingDefinition && !$options->hasOption($value)) {
          $this->setItemValidationResultByCode($entity, $property, $definition->getLogCode(), ['option' => $value]);
        }
      }
    }
  }

  public function validateValueFromItemConfiguration(ItemConfigurationInterface $itemConfiguration, $value, string $client): bool {
    $options = null;
    if($itemConfiguration->hasSetting(OptionItemSettingDefinition::class)) {
      $options = $itemConfiguration->getSetting(OptionItemSettingDefinition::class);
    }

    if ($options instanceof OptionItemSettingDefinition && $options->hasOption($value)) {
      return FALSE;
    }

    return TRUE;
  }

}
