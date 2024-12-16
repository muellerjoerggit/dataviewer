<?php

namespace App\Database\TableReference;

use App\Database\DaViQueryBuilder;
use App\DaViEntity\Schema\EntitySchema;

interface TableReferenceHandlerInterface {

	public function joinTableToQueryBuilder(DaViQueryBuilder $queryBuilder, TableReferenceConfigurationInterface $tableReferenceConfiguration, EntitySchema $fromSchema): void;

}
