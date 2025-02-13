<?php

namespace App\Item\Property;

use App\Item\ItemHandler_AdditionalData\Attribute\AdditionalDataItemHandlerDefinitionInterface;
use App\Item\ItemHandler_EntityReference\Attribute\EntityReferenceItemHandlerDefinitionInterface;
use App\Item\ItemHandler_Formatter\Attribute\FormatterItemHandlerDefinitionInterface;
use App\Item\ItemHandler_PreRendering\Attribute\PreRenderingItemHandlerDefinitionInterface;
use App\Item\ItemHandler_Validator\Attribute\ValidatorItemHandlerDefinitionInterface;
use App\Item\Property\Attribute\DatabaseColumnAttr;
use App\Item\Property\Attribute\PropertyAttr;
use App\Item\Property\Attribute\PropertySettingInterface;
use Generator;
use ReflectionProperty;

class PropertyAttributesContainer {

  private PropertyAttr $propertyAttr;

  /**
   * @var PropertySettingInterface[]
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

  private DatabaseColumnAttr $databaseAttr;

  public function __construct(
    private readonly ReflectionProperty $property,
  ) {}

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

  public function addPropertySetting(PropertySettingInterface $setting): PropertyAttributesContainer {
    $this->propertySettings[] = $setting;
    return $this;
  }

  public function hasPropertySetting(): bool {
    return !empty($this->propertySettings);
  }

  /**
   * @return Generator<PropertySettingInterface>
   */
  public function iteratePropertySetting(): Generator {
    foreach ($this->propertySettings as $setting) {
      yield $setting;
    }
  }

  public function getDatabaseAttr(): DatabaseColumnAttr {
    return $this->databaseAttr;
  }

  public function setDatabaseAttr(DatabaseColumnAttr $databaseAttr): PropertyAttributesContainer {
    $this->databaseAttr = $databaseAttr;
    return $this;
  }

  public function hasDatabaseAttr(): bool {
    return isset($this->databaseAttr);
  }

  public function isValid(): bool {
    return isset($this->propertyAttr);
  }

}