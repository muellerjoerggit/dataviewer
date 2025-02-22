<?php

namespace App\Item\Property;

use App\Database\TableReference\TableReferencePropertyDefinition;
use App\Item\ItemHandler_AdditionalData\Attribute\AdditionalDataItemHandlerDefinitionInterface;
use App\Item\ItemHandler_EntityReference\Attribute\EntityReferenceItemHandlerDefinitionInterface;
use App\Item\ItemHandler_Formatter\Attribute\FormatterItemHandlerDefinitionInterface;
use App\Item\ItemHandler_PreRendering\Attribute\PreRenderingItemHandlerDefinitionInterface;
use App\Item\ItemHandler_Validator\Attribute\ValidatorItemHandlerDefinitionInterface;
use App\Item\Property\Attribute\DatabaseColumnDefinition;
use App\Item\Property\Attribute\PropertyAttr;
use App\Item\Property\Attribute\ItemSettingInterface;
use Generator;
use ReflectionProperty;

class PropertyAttributesContainer {

  private PropertyConfiguration $propertyConfiguration;

  private PropertyAttr $propertyAttr;

  /**
   * @var ItemSettingInterface[]
   */
  private array $propertySettings = [];

  /**
   * @var ValidatorItemHandlerDefinitionInterface[]
   */
  private array $validatorItemHandlers = [];

  private PreRenderingItemHandlerDefinitionInterface $preRenderingItemHandlerDefinition;

  private FormatterItemHandlerDefinitionInterface $formatterItemHandlerDefinition;

  private EntityReferenceItemHandlerDefinitionInterface $referenceItemHandlerDefinition;

  private AdditionalDataItemHandlerDefinitionInterface $additionalDataItemHandlerDefinition;

  private DatabaseColumnDefinition $databasePropertyDefinition;

  private TableReferencePropertyDefinition $tableReferencePropertyDefinition;

  public function __construct(
    private readonly ReflectionProperty $property,
  ) {}

  public function hasPropertyConfiguration(): bool {
    return isset($this->propertyConfiguration);
  }

  public function getPropertyConfiguration(): PropertyConfiguration {
    return $this->propertyConfiguration;
  }

  public function setPropertyConfiguration(PropertyConfiguration $propertyConfiguration): PropertyAttributesContainer {
    $this->propertyConfiguration = $propertyConfiguration;
    return $this;
  }

  public function getPropertyName(): string {
    return $this->property->getName();
  }

  public function getPropertyAttr(): PropertyAttr {
    return $this->propertyAttr;
  }

  public function setPropertyAttr(PropertyAttr $propertyAttr): PropertyAttributesContainer {
    $this->propertyAttr = $propertyAttr;
    return $this;
  }

  public function addValidatorItemHandler(ValidatorItemHandlerDefinitionInterface $handler): PropertyAttributesContainer {
    $this->validatorItemHandlers[] = $handler;
    return $this;
  }

  public function setPreRenderingItemHandlerDefinition(PreRenderingItemHandlerDefinitionInterface $preRenderingItemHandlerDefinition): PropertyAttributesContainer {
    $this->preRenderingItemHandlerDefinition = $preRenderingItemHandlerDefinition;
    return $this;
  }

  public function setFormatterItemHandlerDefinition(FormatterItemHandlerDefinitionInterface $formatterItemHandlerDefinition): PropertyAttributesContainer {
    $this->formatterItemHandlerDefinition = $formatterItemHandlerDefinition;
    return $this;
  }

  public function setReferenceItemHandlerDefinition(EntityReferenceItemHandlerDefinitionInterface $referenceItemHandlerDefinition): PropertyAttributesContainer {
    $this->referenceItemHandlerDefinition = $referenceItemHandlerDefinition;
    return $this;
  }

  public function setAdditionalDataItemHandlerDefinition(AdditionalDataItemHandlerDefinitionInterface $additionalDataItemHandlerDefinition): PropertyAttributesContainer {
    $this->additionalDataItemHandlerDefinition = $additionalDataItemHandlerDefinition;
    return $this;
  }

  public function iterateItemHandlerDefinitions(): Generator {
    if(isset($this->preRenderingItemHandlerDefinition)) {
      yield $this->preRenderingItemHandlerDefinition;
    }

    if(isset($this->formatterItemHandlerDefinition)) {
      yield $this->formatterItemHandlerDefinition;
    }

    if(isset($this->referenceItemHandlerDefinition)) {
      yield $this->referenceItemHandlerDefinition;
    }

    if(isset($this->additionalDataItemHandlerDefinition)) {
      yield $this->additionalDataItemHandlerDefinition;
    }

    foreach ($this->validatorItemHandlers as $validatorItemHandler) {
      yield $validatorItemHandler;
    }
  }

  public function addPropertySetting(ItemSettingInterface $setting): PropertyAttributesContainer {
    $this->propertySettings[] = $setting;
    return $this;
  }

  public function hasPropertySetting(): bool {
    return !empty($this->propertySettings);
  }

  /**
   * @return Generator<ItemSettingInterface>
   */
  public function iteratePropertySetting(): Generator {
    foreach ($this->propertySettings as $setting) {
      yield $setting;
    }
  }

  public function getDatabasePropertyDefinition(): DatabaseColumnDefinition {
    return $this->databasePropertyDefinition;
  }

  public function setDatabasePropertyDefinition(DatabaseColumnDefinition $databasePropertyDefinition): PropertyAttributesContainer {
    $this->databasePropertyDefinition = $databasePropertyDefinition;
    return $this;
  }

  public function hasDatabasePropertyDefinition(): bool {
    return isset($this->databasePropertyDefinition);
  }

  public function getTableReferencePropertyDefinition(): TableReferencePropertyDefinition {
    return $this->tableReferencePropertyDefinition;
  }

  public function setTableReferencePropertyDefinition(TableReferencePropertyDefinition $tableReferencePropertyDefinition): PropertyAttributesContainer {
    $this->tableReferencePropertyDefinition = $tableReferencePropertyDefinition;
    return $this;
  }

  public function hasTableReferencePropertyDefinition(): bool {
    return isset($this->tableReferencePropertyDefinition);
  }

  public function isValid(): bool {
    return isset($this->propertyAttr);
  }

}