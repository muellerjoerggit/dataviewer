<?php

namespace App\Services\EntityActionHandler;

use App\DaViEntity\EntityInterface;
use App\Services\EntityAction\EntityActionConfigAttrInterface;
use App\Services\EntityAction\UrlEntityActionHandlerInterface;

class NullEntityActionHandler implements UrlEntityActionHandlerInterface {

  public function generateUrl(EntityActionConfigAttrInterface $config, EntityInterface $entity): array {
    return [];
  }

}
