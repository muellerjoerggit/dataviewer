<?php

namespace App\Development\Controller;

use App\Database\SqlFilter\SqlFilterDefinitionBuilder;
use App\Database\SqlFilter\SqlFilterDefinitionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SqlFilter extends AbstractController {

  public function filterHash(SqlFilterDefinitionBuilder $filterDefinitionBuilder): void {
    $filterArray = [
      SqlFilterDefinitionInterface::YAML_KEY_NAME => 'Test',
      SqlFilterDefinitionInterface::YAML_KEY_HANDLER => 'CommonTextFilterHandler',
      "values_definition" => [
        "input" => "String",
        "cardinality" => "singleValue"
      ]
    ];
    dd($filterDefinitionBuilder->calculateFilterHash($filterArray));
  }

}