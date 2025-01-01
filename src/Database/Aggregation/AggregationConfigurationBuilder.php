<?php

namespace App\Database\Aggregation;

class AggregationConfigurationBuilder {

  public const string YAML_PARAMETER_TITLE = 'title';
  public const string YAML_PARAMETER_DESCRIPTION = 'description';
  public const string YAML_PARAMETER_HANDLER = 'handler';
  public const string YAML_PARAMETER_PROPERTIES = 'properties';

  public function buildAggregationConfiguration(array $configurationArray, string $key): AggregationConfiguration {
    $aggregationConfiguration = new AggregationConfiguration($key);

    foreach ($configurationArray as $configKey => $config) {
      switch ($configKey) {
        case self::YAML_PARAMETER_TITLE:
          $aggregationConfiguration->setTitle($config);
          break;
        case self::YAML_PARAMETER_DESCRIPTION:
          $aggregationConfiguration->setDescription($config);
          break;
        case self::YAML_PARAMETER_HANDLER:
          $aggregationConfiguration->setHandler($config);
          break;
      }
    }

    return $aggregationConfiguration;
  }

}
