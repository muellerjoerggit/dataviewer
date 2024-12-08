<?php

namespace App\Logger\LogItemPreRendering;

use App\Logger\LogItems\LogItemInterface;
use App\Services\AbstractLocator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class LogItemPreRenderingHandlerLocator extends AbstractLocator {

	public function __construct(
    #[AutowireLocator('log_item_pre_rendering')]
    ServiceLocator $services
  ) {
		parent::__construct($services);
	}

	public function getLogItemPreRenderingHandlerFromLogItem(LogItemInterface $logItem): LogItemPreRenderingHandlerInterface {

		$handlerName = $logItem->getPreRenderingHandler();
		if($handlerName && $this->has($handlerName)) {
			return $this->get($handlerName);
		} else {
			return $this->get(CommonLogItemPreRenderingHandler::class);
		}
	}

}
