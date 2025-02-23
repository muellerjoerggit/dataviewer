<?php

namespace App\Database\AggregationHandler\Attribute;

interface AggregationDefinitionInterface {

  public function getAggregationHandlerClass(): string;

  public function getTitle(): string;

  public function getDescription(): string;

  public function isValid(): bool;

}