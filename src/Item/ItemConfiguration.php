<?php

namespace App\Item;

use App\Item\ItemHandler\ItemHandlerInterface;
use App\Item\ItemHandler_AdditionalData\Attribute\AdditionalDataHandlerDefinitionInterface;
use App\Item\ItemHandler_EntityReference\Attribute\EntityReferenceItemHandlerDefinitionInterface;
use App\Item\ItemHandler_Formatter\Attribute\FormatterItemHandlerDefinitionInterface;
use App\Item\ItemHandler_PreRendering\Attribute\PreRenderingItemHandlerDefinitionInterface;
use App\Item\ItemHandler_Validator\Attribute\ValidatorItemHandlerDefinitionInterface;
use App\Item\Property\Attribute\ItemSettingInterface;
use App\Services\Version\VersionInformation;
use Generator;

class ItemConfiguration implements ItemConfigurationInterface {

  protected int $cardinality = Cardinality::SINGLE;

  protected int $dataType;

  protected string $label;

  protected string $description;

  protected array $settings = [];

  /**
   * @var ValidatorItemHandlerDefinitionInterface[]
   */
  private array $validatorItemHandlers = [];

  private PreRenderingItemHandlerDefinitionInterface $preRenderingItemHandlerDefinition;

  private FormatterItemHandlerDefinitionInterface $formatterItemHandlerDefinition;

  private EntityReferenceItemHandlerDefinitionInterface $referenceItemHandlerDefinition;

  private AdditionalDataHandlerDefinitionInterface $additionalDataHandlerDefinition;

  protected VersionInformation $version;

  public function __construct(
    protected readonly string $name
  ) {}

  public static function createNullConfiguration(): ItemConfigurationInterface {
    return new ItemConfiguration('NullItem');
  }

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
    return $this->getCardinality() === Cardinality::MULTIPLE;
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

  /** ############################################# pre rendering item handler  */
  public function setPreRenderingItemHandlerDefinition(PreRenderingItemHandlerDefinitionInterface $preRenderingItemHandlerDefinition): ItemConfigurationInterface {
    $this->preRenderingItemHandlerDefinition = $preRenderingItemHandlerDefinition;
    return $this;
  }

  public function getPreRenderingHandlerDefinition(): PreRenderingItemHandlerDefinitionInterface {
    return $this->preRenderingItemHandlerDefinition;
  }

  /** ############################################# Validator item handler  */
  public function addValidatorItemHandlerDefinition(ValidatorItemHandlerDefinitionInterface $handler): ItemConfigurationInterface {
    $this->validatorItemHandlers[$handler::class][] = $handler;
    return $this;
  }

  public function hasValidatorHandlerDefinition(): bool {
    return !empty($this->validatorItemHandlers);
  }

  /**
   * @return Generator<ValidatorItemHandlerDefinitionInterface>
   */
  public function iterateValidatorItemHandlerDefinitions(): Generator {
    foreach ($this->validatorItemHandlers as $validatorItemHandlerDefinitions) {
      foreach ($validatorItemHandlerDefinitions as $validatorItemHandlerDefinition) {
        yield $validatorItemHandlerDefinition;
      }
    }
  }

  /**
   * @return Generator<ValidatorItemHandlerDefinitionInterface>
   */
  public function iterateValidatorItemHandlerDefinitionsByClass(string $definitionClass): Generator {
    if(!empty($this->validatorItemHandlers[$definitionClass])) {
      foreach ($this->validatorItemHandlers[$definitionClass] as $validatorItemHandlerDefinition) {
        yield $validatorItemHandlerDefinition;
      }
    } else {
      yield from [];
    }
  }

  /** ############################################# Entity reference item handler  */
  public function setReferenceItemHandlerDefinition(EntityReferenceItemHandlerDefinitionInterface $referenceItemHandlerDefinition): ItemConfigurationInterface {
    $this->referenceItemHandlerDefinition = $referenceItemHandlerDefinition;
    return $this;
  }

  public function hasEntityReferenceHandler(): bool {
    return isset($this->referenceItemHandlerDefinition) && $this->referenceItemHandlerDefinition->isValid();
  }

  public function getReferenceItemHandlerDefinition(): EntityReferenceItemHandlerDefinitionInterface {
    return $this->referenceItemHandlerDefinition;
  }

  /** ############################################# additional data item handler  */
  public function setAdditionalDataHandlerDefinition(AdditionalDataHandlerDefinitionInterface $additionalDataHandlerDefinition): ItemConfigurationInterface {
    $this->additionalDataHandlerDefinition = $additionalDataHandlerDefinition;
    return $this;
  }

  public function getAdditionalDataHandlerDefinition(): AdditionalDataHandlerDefinitionInterface {
    return $this->additionalDataHandlerDefinition;
  }

  public function hasAdditionalDataHandlerHandler(): bool {
    return isset($this->additionalDataHandlerDefinition) && $this->additionalDataHandlerDefinition->isValid();
  }

  /** ############################################# Formatter item handler  */
  public function setFormatterItemHandlerDefinition(FormatterItemHandlerDefinitionInterface $formatterItemHandlerDefinition): ItemConfigurationInterface {
    $this->formatterItemHandlerDefinition = $formatterItemHandlerDefinition;
    return $this;
  }
  public function hasFormatterHandler(): bool {
    return isset($this->formatterItemHandlerDefinition) && $this->formatterItemHandlerDefinition->isValid();
  }

  public function getFormatterItemHandlerDefinition(): FormatterItemHandlerDefinitionInterface {
    return $this->formatterItemHandlerDefinition;
  }

  public function getSetting($definitionClass): ItemSettingInterface {
    return $this->settings[$definitionClass];
  }

  public function hasSetting($definitionClass): bool {
    return isset($this->settings[$definitionClass]);
  }

  public function addSetting(ItemSettingInterface $definition): ItemConfigurationInterface {
    $this->settings[$definition->getClass()] = $definition;
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


}