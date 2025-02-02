<?php

namespace App\Services\EntityAction;

use App\DaViEntity\EntityInterface;

interface UrlEntityActionHandlerInterface {

  public function generateUrl(EntityActionConfigAttrInterface $config, EntityInterface $entity): array;

}