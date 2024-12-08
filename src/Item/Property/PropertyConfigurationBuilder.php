<?php

namespace App\Item\Property;

use App\Database\SqlFilter\FilterGroup;
use App\Database\SqlFilter\SqlFilterDefinitionBuilder;
use App\Database\SqlFilter\SqlFilterDefinitionInterface;
use App\Database\SqlFilter\SqlGeneratedFilterDefinition;
use App\Database\SqlFilter\SqlGeneratedFilterRegister;
use App\DaViEntity\Schema\EntitySchema;
use App\Item\ItemConfigurationInterface;
use App\Item\ItemInterface;
use App\Services\DirectoryFileRegister;

class PropertyConfigurationBuilder {

  public function __construct(
    private readonly DirectoryFileRegister $directoryFileRegister,
    private readonly SqlFilterDefinitionBuilder $sqlFilterDefinitionsBuilder,
    private readonly SqlGeneratedFilterRegister $sqlGeneratedFilterRegister,
  ) {}

  public function buildPropertyConfiguration(array $config, string $propertyName, EntitySchema $schema): PropertyConfiguration {
    $propertyConfiguration = $this->createPropertyConfiguration($propertyName);
    if(isset($config[ItemConfigurationInterface::YAML_PARAM_PRE_DEFINED])) {
      $this->buildPreDefinedConfiguration($config, $propertyConfiguration, $schema);
    }

    return $this->fillPropertyConfiguration($config, $propertyConfiguration, $schema);
  }

  private function buildPreDefinedConfiguration(array $config, PropertyConfiguration $propertyConfiguration, EntitySchema $schema): void {
    $preDefined = $config[ItemConfigurationInterface::YAML_PARAM_PRE_DEFINED];
    if(!is_array($preDefined)) {
      $preDefined = [$preDefined];
    }

    $preDefinedConfigurations = $this->directoryFileRegister->getPreDefinedPropertyConfiguration();

    $preDefined = array_map(function($preDefinedName) use ($preDefinedConfigurations) {
      return $preDefinedConfigurations[$preDefinedName] ?? [];
    }, $preDefined);

    foreach($preDefined as $preDefinedConfig) {
      if(empty($preDefinedConfig)) {
        continue;
      }

      $propertyConfiguration = $this->fillPropertyConfiguration($preDefinedConfig, $propertyConfiguration, $schema);
    }
  }

  private function createPropertyConfiguration(string $propertyName): ?PropertyConfiguration {
    return new PropertyConfiguration($propertyName);
  }

  private function fillPropertyConfiguration(array $config, PropertyConfiguration $propertyConfiguration, EntitySchema $schema): PropertyConfiguration {
    $this->fillBasic($config, $propertyConfiguration);
    $this->fillDatabase($config, $propertyConfiguration, $schema);

    if(isset($config[PropertyConfiguration::YAML_PARAM_HANDLER])) {
      $this->fillHandler($config, $propertyConfiguration);
    }

    if(isset($config[PropertyConfiguration::YAML_PARAM_FILTER])) {
      $this->fillFilter($config, $propertyConfiguration, $schema);
    }

    return $propertyConfiguration;
  }

  private function fillBasic(array $config, PropertyConfiguration $propertyConfiguration): void {
    if(isset($config[ItemConfigurationInterface::YAML_PARAM_CARDINALITY])) {
      $cardinality = $config[ItemConfigurationInterface::YAML_PARAM_CARDINALITY] === ItemConfigurationInterface::YAML_PARAM_VALUE_MULTIPLE ? ItemConfigurationInterface::CARDINALITY_MULTIPLE : ItemConfigurationInterface::CARDINALITY_SINGLE;
      $propertyConfiguration->setCardinality($cardinality);
    }

    if(isset($config[ItemConfigurationInterface::YAML_PARAM_DATA_TYPE])) {
      $dataType = match ($config[ItemConfigurationInterface::YAML_PARAM_DATA_TYPE]) {
        'Integer' => ItemInterface::DATA_TYPE_INTEGER,
        'String' => ItemInterface::DATA_TYPE_STRING,
        'Enum' => ItemInterface::DATA_TYPE_ENUM,
        'Boolean' => ItemInterface::DATA_TYPE_BOOL,
        'Datetime' => ItemInterface::DATA_TYPE_DATE_TIME,
        'Time' => ItemInterface::DATA_TYPE_TIME,
        'Table' => ItemInterface::DATA_TYPE_TABLE,
        'Float' => ItemInterface::DATA_TYPE_FLOAT,
        default => ItemInterface::DATA_TYPE_UNKNOWN
      };
      $propertyConfiguration->setDataType($dataType);
    }

    if(isset($config[ItemConfigurationInterface::YAML_PARAM_LABEL])) {
      $propertyConfiguration->setLabel($config[ItemConfigurationInterface::YAML_PARAM_LABEL]);
    }

    if(isset($config[ItemConfigurationInterface::YAML_PARAM_DESCRIPTION])) {
      $propertyConfiguration->setDescription($config[ItemConfigurationInterface::YAML_PARAM_DESCRIPTION]);
    }

    $settings = $config[ItemConfigurationInterface::YAML_PARAM_SETTINGS] ?? null;
    if(is_array($settings)) {
      $propertyConfiguration->mergeSettings($settings);
    }
  }

  private function fillDatabase(array $config, PropertyConfiguration $propertyConfiguration, EntitySchema $schema): void {
    if(isset($config[PropertyConfiguration::YAML_PARAM_COLUMN])) {
      $propertyConfiguration->setColumn($schema->getBaseTable() . '.' . $config[PropertyConfiguration::YAML_PARAM_COLUMN]);
    }
  }

  private function fillFilter(array $config, PropertyConfiguration $propertyConfiguration, EntitySchema $schema): void {
    $property = $propertyConfiguration->getItemName();
    $filterGroup = new FilterGroup(
      SqlFilterDefinitionInterface::FILTER_PREFIX_GENERATED . '_' . $property,
      $property
    );

    foreach ($config[PropertyConfiguration::YAML_PARAM_FILTER] as $filter) {
      if(!is_array($filter)) {
        $filter[SqlFilterDefinitionInterface::YAML_KEY_HANDLER] = $filter;
      }

      $hashName = $this->sqlFilterDefinitionsBuilder->calculateFilterHash($filter);

      if($this->sqlGeneratedFilterRegister->hasFilter($hashName)) {
        $filterDefinition = $this->sqlGeneratedFilterRegister->getFilter($hashName);
      } else {
        $filterDefinition = SqlGeneratedFilterDefinition::create($hashName, $filter);
        $this->sqlFilterDefinitionsBuilder->fillEntityFilterDefinition($filterDefinition, $filter);
        $this->sqlGeneratedFilterRegister->addFilter($filterDefinition);
      }

      $schema->addFilter($filterDefinition, $property, $filterGroup);
    }
  }

  private function fillHandler(array $config, PropertyConfiguration $propertyConfiguration): void {
    $propertyConfiguration->fillHandler($config[PropertyConfiguration::YAML_PARAM_HANDLER]);
  }

}