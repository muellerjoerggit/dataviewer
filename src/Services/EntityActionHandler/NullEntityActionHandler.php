<?php

namespace App\Services\EntityActionHandler;

use App\DaViEntity\EntityInterface;
use App\Services\EntityAction\EntityActionDefinitionInterface;
use App\Services\EntityAction\UrlEntityActionHandlerInterface;

class NullEntityActionHandler implements UrlEntityActionHandlerInterface {

  public function generateUrl(EntityActionDefinitionInterface $config, EntityInterface $entity): array {
    return [];
  }

}
