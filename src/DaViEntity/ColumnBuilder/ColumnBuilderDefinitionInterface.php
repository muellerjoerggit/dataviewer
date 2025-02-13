<?php

namespace App\DaViEntity\ColumnBuilder;

interface ColumnBuilderDefinitionInterface {

  public function getEntityColumnBuilderClass(): string;

}