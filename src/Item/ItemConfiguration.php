<?php

namespace App\Item;

use App\Item\ItemHandler\ItemHandlerInterface;
use App\Services\AppNamespaces;

class ItemConfiguration implements ItemConfigurationInterface {

  protected int $cardinality = ItemConfigurationInterface::CARDINALITY_SINGLE;
  protected int $dataType;
  protected string $label;
  protected string $description;

  protected array $handler = [];
  protected array $handlerSettings = [];

  protected array $settings = [];

  public function __construct(
    protected readonly string $name
  ) {}

  public function getItemName(): string {
    return $this->name;
  }

  public function getLabel(): string {
    if (!empty($this->label)) {
      return $this->label;
    } else {
      return $this->name;
    }
  }

  public function setLabel(string $label): ItemConfigurationInterface {
    $this->label = $label;
    return $this;
  }

  public function getDescription(): string {
    return $this->description ?? '';
  }

  public function setDescription(string $description): ItemConfigurationInterface	{
    $this->description = $description;
    return $this;
  }

  public function getCardinality(): int {
    return $this->cardinality;
  }

  public function isCardinalityMultiple(): bool {
    return $this->getCardinality() === ItemConfigurationInterface::CARDINALITY_MULTIPLE;
  }

  public function setCardinality(int $cardinality): ItemConfiguration {
    $this->cardinality = $cardinality;
    return $this;
  }

  public function setDataType(int $dataType): ItemConfiguration {
    $this->dataType = $dataType;
    return $this;
  }

  public function getDataType(): int {
    return $this->dataType ?? 0;
  }

  public function setHandler(string $handlerType, string $handler): ItemConfigurationInterface {
    $this->setHandlerInternal($handlerType, $handler);
    return $this;
  }

  protected function setHandlerInternal(string $handlerType, string $handlerShortName): string {
    $handlerName = $this->getHandlerClass($handlerType, $handlerShortName);

    if($handlerType === ItemHandlerInterface::HANDLER_VALIDATOR) {
      if(isset($this->handler[$handlerType])) {
        $handlers = array_merge($this->handler[$handlerType], [$handlerName]);
      } else {
        $handlers = [$handlerName];
      }

      $this->handler[$handlerType] = $handlers;
    } else {
      $this->handler[$handlerType] = $handlerName;
    }

    return $handlerName;
  }

  protected function getHandlerClass(string $handlerType, string $handlerShortName): string {
    $namespace = match ($handlerType) {
      ItemHandlerInterface::HANDLER_ENTITY_REFERENCE => AppNamespaces::NAMESPACE_ENTITY_REFERENCE_ITEM_HANDLER,
      ItemHandlerInterface::HANDLER_PRE_RENDERING => AppNamespaces::NAMESPACE_PRE_RENDERING_ITEM_HANDLER,
      ItemHandlerInterface::HANDLER_VALUE_FORMATTER => AppNamespaces::NAMESPACE_VALUE_FORMATTER_ITEM_HANDLER,
      ItemHandlerInterface::HANDLER_ADDITIONAL_DATA => AppNamespaces::NAMESPACE_ADDITIONAL_DATA_ITEM_HANDLER,
      ItemHandlerInterface::HANDLER_VALIDATOR => AppNamespaces::NAMESPACE_VALIDATOR_ITEM_HANDLER,
      default => ''
    };

    return AppNamespaces::buildNamespace($namespace, $handlerShortName);
  }

  public function fillHandler(array $handlerArray): ItemConfigurationInterface {
    $validHandlers = [
      ItemHandlerInterface::HANDLER_PRE_RENDERING,
      ItemHandlerInterface::HANDLER_ADDITIONAL_DATA,
      ItemHandlerInterface::HANDLER_VALUE_FORMATTER,
      ItemHandlerInterface::HANDLER_VALIDATOR,
      ItemHandlerInterface::HANDLER_ENTITY_REFERENCE,
    ];

    foreach ($handlerArray as $handlerType => $handler) {
      if(!in_array($handlerType, $validHandlers)) {
        continue;
      }

      if(is_string($handler)) {
        $this->setHandlerInternal($handlerType, $handler);
      } elseif(is_array($handler)) {
        $this->fillHandlerWithSettings($handlerType, $handler);
      }


    }
    return $this;
  }

  protected function fillHandlerWithSettings(string $handlerType, array $handlerArray): void {
    foreach($handlerArray as $handler => $handlerSetting) {
      $handlerName = $this->setHandlerInternal($handlerType, $handler);
      $this->setHandlerSettings($handlerType, $handlerName, $handlerSetting);
    }
  }

  public function getHandlerByType(string $handlerType): string | array | bool {
    if(!$this->hasHandlerByType($handlerType)) {
      return false;
    }

    return $this->handler[$handlerType];
  }

  public function hasHandlerByType($handlerType): bool {
    return isset($this->handler[$handlerType]);
  }

  public function setHandlerSettings(string $handlerType, string $handlerName, array $handlerSettings): ItemConfigurationInterface {
    if($handlerType === ItemHandlerInterface::HANDLER_VALIDATOR) {
      $this->handlerSettings[$handlerType][$handlerName] = $handlerSettings;
    } elseif ($handlerType === ItemHandlerInterface::HANDLER_ENTITY_REFERENCE && isset($handlerSettings['validation'])) {
      $newValidatorSettings = [];
      foreach($handlerSettings['validation'] as $validatorHandler => $validator) {
        $validatorHandlerClass = $this->getHandlerClass(ItemHandlerInterface::HANDLER_VALIDATOR, $validatorHandler);
        $newValidatorSettings[$validatorHandlerClass] = $validator;
      }
      $handlerSettings['validation'] = $newValidatorSettings;
      $this->handlerSettings[$handlerType] = $handlerSettings;
    } else {
      $this->handlerSettings[$handlerType] = $handlerSettings;
    }

    return $this;
  }

  public function getAdditionalDataSetting(): array {
    return $this->handlerSettings[ItemHandlerInterface::HANDLER_ADDITIONAL_DATA] ?? [];
  }

  public function hasEntityReferenceHandler(): bool {
    return $this->hasHandlerByType(ItemHandlerInterface::HANDLER_ENTITY_REFERENCE);
  }

  public function hasAdditionalDataHandler(): bool {
    return $this->hasHandlerByType(ItemHandlerInterface::HANDLER_ADDITIONAL_DATA);
  }

  public function hasFormatterHandler(): bool {
    return $this->hasHandlerByType(ItemHandlerInterface::HANDLER_VALUE_FORMATTER);
  }

  public function getValidatorItemHandlerSettings($handlerName): array {
    return $this->handlerSettings[ItemHandlerInterface::HANDLER_VALIDATOR][$handlerName] ?? [];
  }

  public function hasValidatorHandler(): bool {
    return $this->hasHandlerByType(ItemHandlerInterface::HANDLER_VALIDATOR);
  }

  public function iterateValidatorHandlers(): \Generator {
    foreach ($this->handler[ItemHandlerInterface::HANDLER_VALIDATOR] as $handlerName) {
      $handlerConfig = $this->getValidatorItemHandlerSettings($handlerName);
      yield $handlerName => $handlerConfig;
    }
  }

  public function getEntityReferenceHandlerSetting(): array {
    return $this->handlerSettings[ItemHandlerInterface::HANDLER_ENTITY_REFERENCE] ?? [];
  }

  public function getPreRenderingHandlerSetting(): array {
    return $this->handlerSettings[ItemHandlerInterface::HANDLER_PRE_RENDERING] ?? [];
  }

  public function getSetting($setting, $default = null): mixed {
    return $this->settings[$setting] ?? $default;
  }

  public function mergeSettings(array $settings): ItemConfigurationInterface {
    $currentSettings = $this->settings ?? [];
    $this->settings = array_merge($currentSettings, $settings);
    return $this;
  }

  public function setSetting(string $setting, $value): ItemConfigurationInterface {
    $this->settings[$setting] = $value;
    return $this;
  }

  public function setSettings(array $settings): ItemConfigurationInterface {
    $this->settings = $settings;
    return $this;
  }

}