<?php

namespace App\Item;

use App\Item\ItemHandler\ItemHandlerInterface;
use App\Item\ItemHandler_AdditionalData\Attribute\AdditionalDataItemHandlerDefinitionInterface;
use App\Item\ItemHandler_EntityReference\Attribute\EntityReferenceItemHandlerDefinitionInterface;
use App\Item\ItemHandler_Formatter\Attribute\FormatterItemHandlerDefinitionInterface;
use App\Item\ItemHandler_PreRendering\Attribute\PreRenderingItemHandlerDefinitionInterface;
use App\Item\ItemHandler_Validator\Attribute\ValidatorItemHandlerDefinitionInterface;
use App\Item\ItemHandler_Validator\ValidatorItemHandlerInterface;
use App\Item\Property\Attribute\PropertySettingInterface;
use App\Services\AppNamespaces;
use App\Services\Export\ExportFormatter\ExportFormatterAttributeInterface;
use App\Services\Version\VersionInformation;
use Generator;

class ItemConfiguration implements ItemConfigurationInterface {

  protected int $cardinality = ItemConfigurationInterface::CARDINALITY_SINGLE;

  protected int $dataType;

  protected string $label;

  protected string $description;

  protected array $handler = [];

  protected array $handlerSettings = [];

  protected array $settings = [];

  /**
   * @var ExportFormatterAttributeInterface[]
   */
  protected array $exportFormatter = [];

  /**
   * @var ValidatorItemHandlerDefinitionInterface[]
   */
  private array $validatorItemHandlers = [];

  private PreRenderingItemHandlerDefinitionInterface $preRenderingItemHandlerDefinition;

  private FormatterItemHandlerDefinitionInterface $formatterItemHandlerDefinition;

  private EntityReferenceItemHandlerDefinitionInterface $referenceItemHandlerDefinition;

  private AdditionalDataItemHandlerDefinitionInterface $additionalDataItemHandlerDefinition;

  protected VersionInformation $version;

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

  public function setDescription(string $description): ItemConfigurationInterface {
    $this->description = $description;
    return $this;
  }

  public function isCardinalityMultiple(): bool {
    return $this->getCardinality() === ItemConfigurationInterface::CARDINALITY_MULTIPLE;
  }

  public function getCardinality(): int {
    return $this->cardinality;
  }

  public function setCardinality(int $cardinality): ItemConfiguration {
    $this->cardinality = $cardinality;
    return $this;
  }

  public function getDataType(): int {
    return $this->dataType ?? 0;
  }

  public function setDataType(int $dataType): ItemConfiguration {
    $this->dataType = $dataType;
    return $this;
  }

  public function addValidatorItemHandler(ValidatorItemHandlerDefinitionInterface $handler): ItemConfigurationInterface {
    $this->validatorItemHandlers[] = $handler;
    return $this;
  }

  public function setPreRenderingItemHandlerDefinition(PreRenderingItemHandlerDefinitionInterface $preRenderingItemHandlerDefinition): ItemConfigurationInterface {
    $this->preRenderingItemHandlerDefinition = $preRenderingItemHandlerDefinition;
    return $this;
  }

  public function setFormatterItemHandlerDefinition(FormatterItemHandlerDefinitionInterface $formatterItemHandlerDefinition): ItemConfigurationInterface {
    $this->formatterItemHandlerDefinition = $formatterItemHandlerDefinition;
    return $this;
  }

  public function setReferenceItemHandlerDefinition(EntityReferenceItemHandlerDefinitionInterface $referenceItemHandlerDefinition): ItemConfigurationInterface {
    $this->referenceItemHandlerDefinition = $referenceItemHandlerDefinition;
    return $this;
  }

  public function setAdditionalDataItemHandlerDefinition(AdditionalDataItemHandlerDefinitionInterface $additionalDataItemHandlerDefinition): ItemConfigurationInterface {
    $this->additionalDataItemHandlerDefinition = $additionalDataItemHandlerDefinition;
    return $this;
  }

  public function getPreRenderingItemHandlerDefinition(): PreRenderingItemHandlerDefinitionInterface {
    return $this->preRenderingItemHandlerDefinition;
  }

  public function getFormatterItemHandlerDefinition(): FormatterItemHandlerDefinitionInterface {
    return $this->formatterItemHandlerDefinition;
  }

  public function getReferenceItemHandlerDefinition(): EntityReferenceItemHandlerDefinitionInterface {
    return $this->referenceItemHandlerDefinition;
  }

  public function getAdditionalDataItemHandlerDefinition(): AdditionalDataItemHandlerDefinitionInterface {
    return $this->additionalDataItemHandlerDefinition;
  }

  public function iterateValidatorItemHandlerDefinition(): Generator {
    foreach ($this->validatorItemHandlers as $validatorItemHandlerDefinition) {
      yield $validatorItemHandlerDefinition;
    }
  }

  public function getHandlerByType(string $handlerType): string|array|bool {
    if (!$this->hasHandlerByType($handlerType)) {
      return FALSE;
    }

    return $this->handler[$handlerType];
  }

  public function hasHandlerByType($handlerType): bool {
    return isset($this->handler[$handlerType]);
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

  public function hasValidatorHandler(): bool {
    return $this->hasHandlerByType(ItemHandlerInterface::HANDLER_VALIDATOR);
  }

  public function iterateValidatorHandlers(): Generator {
    foreach ($this->handler[ItemHandlerInterface::HANDLER_VALIDATOR] as $handlerName) {
      $handlerConfig = $this->getValidatorItemHandlerSettings($handlerName);
      yield $handlerName => $handlerConfig;
    }
  }

  public function getValidatorItemHandlerSettings($handlerName): array {
    return $this->handlerSettings[ItemHandlerInterface::HANDLER_VALIDATOR][$handlerName] ?? [];
  }

  public function getEntityReferenceHandlerSetting(): array {
    return $this->handlerSettings[ItemHandlerInterface::HANDLER_ENTITY_REFERENCE] ?? [];
  }

  public function getPreRenderingHandlerSetting(): array {
    return $this->handlerSettings[ItemHandlerInterface::HANDLER_PRE_RENDERING] ?? [];
  }

  public function getSetting($setting): mixed {
    return $this->settings[$setting];
  }

  public function addSetting(PropertySettingInterface $setting): ItemConfigurationInterface {
    $this->settings[$setting->getClass()] = $setting;
    return $this;
  }

  public function getVersion(): VersionInformation | null {
    return $this->version ?? null;
  }

  public function setVersion(VersionInformation $version): ItemConfiguration {
    $this->version = $version;
    return $this;
  }

  public function hasVersion(): bool {
    return isset($this->version);
  }

  public function addExportFormatter(ExportFormatterAttributeInterface $exportFormatter): ItemConfiguration {
    $this->exportFormatter[] = $exportFormatter;
    return $this;
  }

  /**
   * @return Generator<ExportFormatterAttributeInterface>
   */
  public function iterateExportFormatters(): Generator {
    foreach ($this->exportFormatter as $exportFormatter) {
      yield $exportFormatter;
    }
  }



}