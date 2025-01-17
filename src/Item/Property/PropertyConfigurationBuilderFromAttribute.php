<?php

namespace App\Item\Property;

use App\DaViEntity\Schema\EntitySchema;
use App\Item\Property\Attribute\PropertyAttr;
use ReflectionAttribute;
use ReflectionProperty;

class PropertyConfigurationBuilderFromAttribute {

  public function buildPropertyConfiguration(ReflectionProperty $reflectionProperty, EntitySchema $schema): PropertyConfiguration {
    $propertyConfiguration = new PropertyConfiguration($reflectionProperty->getName());

    $this->fillPropertyConfiguration($reflectionProperty, $propertyConfiguration, $schema);

    return $propertyConfiguration;
  }

  private function fillPropertyConfiguration(ReflectionProperty $reflectionProperty, PropertyConfiguration $propertyConfiguration, EntitySchema $schema): bool {
    $attr = $reflectionProperty->getAttributes(PropertyAttr::class);
    $attr = reset($attr);

    if(!$attr instanceof ReflectionAttribute) {
      return false;
    }

    $instance = $attr->newInstance();

    $propertyConfiguration->setCardinality($instance->getCardinality());
    $propertyConfiguration->setDataType($instance->getDataType());

    $label = $instance->getLabel();
    if(!empty($label)) {
      $propertyConfiguration->setLabel($label);
    }
    $description = $instance->getDescription();
    if(!empty($description)) {
      $propertyConfiguration->setDescription($description);
    }

//    $settings = $config[ItemConfigurationInterface::YAML_PARAM_SETTINGS] ?? null;
//    if(is_array($settings)) {
//      $propertyConfiguration->mergeSettings($settings);
//    }

    return true;

  }

}