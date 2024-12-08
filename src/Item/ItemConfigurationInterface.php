<?php

namespace App\Item;

interface ItemConfigurationInterface {

  public const int CARDINALITY_SINGLE = 1;
  public const int CARDINALITY_MULTIPLE = 2;

  public const string YAML_PARAM_PRE_DEFINED = 'preDefined';
  public const string YAML_PARAM_CARDINALITY = 'cardinality';
    public const string YAML_PARAM_VALUE_MULTIPLE = 'multiple';
  public const string YAML_PARAM_DATA_TYPE = 'dataType';
  public const string YAML_PARAM_LABEL = 'label';
  public const string YAML_PARAM_DESCRIPTION = 'description';
  public const string YAML_PARAM_SETTINGS = 'settings';

}