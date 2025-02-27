<?php

namespace App\Database\BaseQuery;

use App\Database\QueryBuilder\QueryBuilderInterface;
use App\DaViEntity\EntityInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('data_mapper.base_query')]
interface BaseQueryInterface {

  public function buildQueryFromSchema(string | EntityInterface $entityTypeClass, string $client, array $options = []): QueryBuilderInterface;

}