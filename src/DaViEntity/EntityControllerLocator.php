<?php

namespace App\DaViEntity;

use App\DaViEntity\EntityTypes\NullEntity\NullEntityController;
use App\Services\AbstractLocator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class EntityControllerLocator extends AbstractLocator {

  public function __construct(
    #[AutowireLocator('entity_management.entity_controller')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getController(string $id): EntityControllerInterface {
    if (!$this->has($id)) {
      return $this->get(NullEntityController::class);
    }

    return $this->get($id);
  }

}