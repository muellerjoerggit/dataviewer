<?php

namespace App\Database\SqlFilter;

use App\Database\SqlFilterHandler\NullFilterHandler;
use App\Services\AbstractLocator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;
use App\Database\SqlFilterHandler\Attribute\SqlFilterDefinitionInterface;

class SqlFilterHandlerLocator extends AbstractLocator {

  public function __construct(
    #[AutowireLocator('sql_filter_handler')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getFilterHandlerFromFilterDefinition(SqlFilterDefinitionInterface $filterDefinition): SqlFilterHandlerInterface {
    $handler = $filterDefinition->getFilterHandler();

    if ($handler && $this->has($handler)) {
      return $this->get($handler);
    } else {
      return $this->get(NullFilterHandler::class);
    }
  }

}
