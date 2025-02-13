<?php

namespace App\Database\SqlFilter;

/**
 * @deprecated
 */
interface SqlFilterDefinitionInterface {

  public const int FILTER_TYPE_UNKNOWN = 0;
  public const int FILTER_TYPE_STANDALONE = 1;
  public const int FILTER_TYPE_GENERATED = 2;

  public const string FILTER_PREFIX_STANDALONE = 'sta';
  public const string FILTER_PREFIX_GENERATED = 'gen';

  public const string YAML_KEY_NAME = 'name';
  public const string YAML_KEY_TYPE = 'type';
  public const string YAML_KEY_HANDLER = 'handler';
  public const string YAML_KEY_TITLE = 'title';
  public const string YAML_KEY_DESCRIPTION = 'description';
  public const string YAML_KEY_PROPERTY = 'property';
}
