<?php

namespace App\Services\EntityAction;

use App\DaViEntity\EntityInterface;
use App\Services\EntityActionHandler\NullEntityActionHandler;

class EntityActionPreRenderingBuilder {

  public function __construct(
    private readonly EntityActionHandlerLocator $handlerLocator,
  ) {}

  public function buildEntityActions(EntityInterface $entity): array {
    $schema = $entity->getSchema();
    $ret = [];

    foreach ($schema->iterateEntityActions() as $action) {
      $handler = $this->handlerLocator->getEntityActionHandler($action);

      if($handler instanceof NullEntityActionHandler) {
        continue;
      } elseif ($handler instanceof UrlEntityActionHandlerInterface) {
        $actionData = $handler->generateUrl($action, $entity);
      }

      if(empty($actionData)) {
        continue;
      }

      $ret[] = $actionData;
    }

    return $ret;
  }

}