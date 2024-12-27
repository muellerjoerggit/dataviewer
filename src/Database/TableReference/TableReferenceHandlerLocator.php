<?php

namespace App\Database\TableReference;

use App\Database\TableReferenceHandler\NullTableReferenceHandler;
use App\Services\AbstractLocator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class TableReferenceHandlerLocator extends AbstractLocator {

	public function __construct(
    #[AutowireLocator('database.table_reference')]
    ServiceLocator $services
  ) {
		parent::__construct($services);
	}

	public function getTableHandlerFromConfiguration(TableReferenceConfiguration $tableReferenceConfiguration): TableReferenceHandlerInterface {
		$handler = $tableReferenceConfiguration->getHandler();

		if($this->has($handler)) {
			return $this->get($handler);
		} else {
			return $this->get(NullTableReferenceHandler::class);
		}
	}

}
