<?php

namespace App\Services\EntityAction;

use App\DaViEntity\EntityInterface;

interface UrlEntityActionHandlerInterface {

  public function generateUrl(EntityActionDefinitionInterface $config, EntityInterface $entity): array;

}