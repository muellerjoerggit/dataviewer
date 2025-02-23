<?php

namespace App\Database\TableReferenceHandler;


use App\Database\BaseQuery\BaseQueryLocator;
use App\Database\DatabaseLocator;
use App\Database\DaViQueryBuilder;
use App\Database\TableReferenceHandler\Attribute\CommonTableReferenceDefinition;
use App\Database\TableReferenceHandler\Attribute\TableReferenceDefinitionInterface;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;

class CommonTableReferenceHandler extends AbstractTableReferenceHandler {

	public function __construct(
    protected readonly DatabaseLocator $databaseLocator,
    EntityTypeSchemaRegister $schemaRegister,
    BaseQueryLocator $baseQueryLocator,
  ) {
    parent::__construct($schemaRegister, $baseQueryLocator);
  }

}
