<?php

namespace App\Database\Aggregation;

use App\Database\AggregationHandler\Attribute\AggregationDefinitionInterface;
use App\Database\AggregationHandler\NullAggregationHandler;
use App\Services\AbstractLocator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class AggregationHandlerLocator extends AbstractLocator {

  public function __construct(
    #[AutowireLocator('aggregation_handler')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getAggregationHandler(AggregationDefinitionInterface $definition): AggregationHandlerInterface {
    $handler = $definition->getAggregationHandlerClass();
    if ($this->has($handler)) {
      return $this->get($handler);
    } else {
      return $this->get(NullAggregationHandler::class);
    }
  }

}
