<?php

namespace App\Services;

use Symfony\Component\DependencyInjection\ServiceLocator;

abstract class AbstractLocator {

  public function __construct(
    protected readonly ServiceLocator $services
  ) {}

  protected function get($id) {
    return $this->services->get($id);
  }

  public function has($id): bool {
    return $this->services->has($id);
  }

  public function getProvidedServices(): array {
    return $this->services->getProvidedServices();
  }

}