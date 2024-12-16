<?php

namespace App\Database\Aggregation;

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

  public function getAggregationHandler(AggregationConfiguration $configuration): AggregationHandlerInterface {
    $handlerName = $configuration->getHandler();
    if ($handlerName && $this->has($handlerName)) {
      return $this->get($handlerName);
    } else {
      return $this->get(NullAggregationHandler::class);
    }
  }

}
