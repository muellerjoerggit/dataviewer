<?php

namespace App\Services\EntityAction;

use App\Services\AbstractLocator;
use App\Services\EntityActionHandler\NullEntityActionHandler;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class EntityActionHandlerLocator extends AbstractLocator {

  public function __construct(
    #[AutowireLocator('entity_services.entity_action_handler')]
    ServiceLocator $services,
  ) {
    parent::__construct($services);
  }

  public function getEntityActionHandler(EntityActionDefinitionInterface $config) {
    $handler = $config->getHandler();

    if (!$this->has($handler)) {
      return $this->get(NullEntityActionHandler::class);
    }

    return $this->get($handler);
  }

}