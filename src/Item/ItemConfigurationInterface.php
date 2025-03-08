<?php

namespace App\Item;

use App\Item\ItemHandler_AdditionalData\Attribute\AdditionalDataHandlerDefinitionInterface;
use App\Item\ItemHandler_EntityReference\Attribute\EntityReferenceItemHandlerDefinitionInterface;
use App\Item\ItemHandler_Formatter\Attribute\FormatterItemHandlerDefinitionInterface;
use App\Item\ItemHandler_PreRendering\Attribute\PreRenderingItemHandlerDefinitionInterface;
use App\Item\ItemHandler_Validator\Attribute\ValidatorItemHandlerDefinitionInterface;
use App\Item\Property\Attribute\ItemSettingInterface;
use App\Services\Version\VersionInformation;
use Generator;

interface ItemConfigurationInterface {

  public function getItemName(): string;

  public function getLabel(): string;

  public function setLabel(string $label): ItemConfigurationInterface;

  public function getDescription(): string;

  public function setDescription(string $description): ItemConfigurationInterface;

  public function isCardinalityMultiple(): bool;

  public function getCardinality(): int;

  public function setCardinality(int $cardinality): ItemConfiguration;

  public function getDataType(): int;

  public function setDataType(int $dataType): ItemConfiguration;

  public function setPreRenderingItemHandlerDefinition(PreRenderingItemHandlerDefinitionInterface $preRenderingItemHandlerDefinition): ItemConfigurationInterface;

  public function getPreRenderingHandlerDefinition(): PreRenderingItemHandlerDefinitionInterface;

  public function addValidatorItemHandlerDefinition(ValidatorItemHandlerDefinitionInterface $handler): ItemConfigurationInterface;

  public function hasValidatorHandlerDefinition(): bool;

  public function iterateValidatorItemHandlerDefinitions(): Generator;

  public function iterateValidatorItemHandlerDefinitionsByClass(string $definitionClass): Generator;

  public function setReferenceItemHandlerDefinition(EntityReferenceItemHandlerDefinitionInterface $referenceItemHandlerDefinition): ItemConfigurationInterface;

  public function hasEntityReferenceHandler(): bool;

  public function getReferenceItemHandlerDefinition(): EntityReferenceItemHandlerDefinitionInterface;

  public function setAdditionalDataHandlerDefinition(AdditionalDataHandlerDefinitionInterface $additionalDataHandlerDefinition): ItemConfigurationInterface;

  public function getAdditionalDataHandlerDefinition(): AdditionalDataHandlerDefinitionInterface;

  public function hasAdditionalDataHandlerHandler(): bool;

  public function setFormatterItemHandlerDefinition(FormatterItemHandlerDefinitionInterface $formatterItemHandlerDefinition): ItemConfigurationInterface;

  public function hasFormatterHandler(): bool;

  public function getFormatterItemHandlerDefinition(): FormatterItemHandlerDefinitionInterface;

  public function getSetting($definitionClass): ItemSettingInterface;

  public function hasSetting($definitionClass): bool;

  public function addSetting(ItemSettingInterface $definition): ItemConfigurationInterface;

  public function getVersion(): VersionInformation | null;

  public function setVersion(VersionInformation $version): ItemConfiguration;

  public function hasVersion(): bool;


}