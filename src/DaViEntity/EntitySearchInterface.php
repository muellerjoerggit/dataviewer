<?php

namespace App\DaViEntity;

use App\DaViEntity\Schema\EntitySchema;

interface EntitySearchInterface {

  public function getEntityListFromSearchString(string $client, EntitySchema $schema, string $searchString, string $uniqueColumn): array;

}