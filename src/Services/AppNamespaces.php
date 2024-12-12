<?php

namespace App\Services;

class AppNamespaces {

  public const string ENTITY_TYPE_NAMESPACE = 'App\\DaViEntity\\EntityTypes';

  public const string NAMESPACE_ENTITY_REFERENCE_ITEM_HANDLER = 'App\\Item\\ItemHandler_EntityReference';

  public const string NAMESPACE_PRE_RENDERING_ITEM_HANDLER = 'App\\Item\\ItemHandler_PreRendering';

  public const string NAMESPACE_VALUE_FORMATTER_ITEM_HANDLER = 'App\\Item\\ItemHandler_ValueFormatter';

  public const string NAMESPACE_ADDITIONAL_DATA_ITEM_HANDLER = 'App\\Item\\ItemHandler_AdditionalData';

  public const string NAMESPACE_VALIDATOR_ITEM_HANDLER = 'App\\Item\\ItemHandler_Validator';

  public const string SQL_FILTER_HANDLER = 'App\\Database\\SqlFilterHandler';

  public const string AGGREGATION_HANDLER = 'App\\Database\\AggregationHandler';
  public const string TABLE_REFERENCE_HANDLER = 'App\\Database\\TableReferenceHandler';

  public const string SYMFONY_CONSTRAINTS = 'Symfony\\Component\\Validator\\Constraints';

  public static function buildNamespace(string $namespace, string ...$parts): string {
    $namespace = str_ends_with($namespace, '\\') ? substr($namespace, 0, -1) : $namespace;
    return array_reduce($parts, function($result, $part) {
      return $result . '\\' . $part;
    }, $namespace);
  }

}