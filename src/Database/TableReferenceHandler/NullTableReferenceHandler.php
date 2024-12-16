<?php

namespace App\Database\TableReferenceHandler;

use App\Database\DaViQueryBuilder;
use App\Database\TableReference\TableReferenceConfigurationInterface;
use App\Database\TableReference\TableReferenceHandlerInterface;
use App\DaViEntity\Schema\EntitySchema;

class NullTableReferenceHandler implements TableReferenceHandlerInterface {

	public function joinTableToQueryBuilder(DaViQueryBuilder $queryBuilder, TableReferenceConfigurationInterface $tableReferenceConfiguration, EntitySchema $fromSchema): void {}

}
