<?php

namespace App\Database\SqlFilter;

use App\Database\SqlFilterHandler\NullFilterHandler;
use App\Services\AbstractLocator;
use App\Services\AppNamespaces;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class SqlFilterHandlerLocator extends AbstractLocator {

  public function __construct(
    #[AutowireLocator('sql_filter_handler')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

	public function getFilterHandlerFromFilterDefinition(SqlFilterDefinitionInterface $filterDefinition): SqlFilterHandlerInterface {
		$handlerName = AppNamespaces::buildNamespace(AppNamespaces::SQL_FILTER_HANDLER, $filterDefinition->getHandler());

		if($handlerName && $this->has($handlerName)) {
			return $this->get($handlerName);
		} else {
			return $this->get(NullFilterHandler::class);
		}
	}
}
