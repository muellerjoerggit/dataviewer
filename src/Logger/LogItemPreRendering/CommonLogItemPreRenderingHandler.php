<?php

namespace App\Logger\LogItemPreRendering;

use App\Logger\LogItems\LogItemInterface;

class CommonLogItemPreRenderingHandler implements LogItemPreRenderingHandlerInterface {

	public function preRenderLogItem(LogItemInterface $logItem): array {
		return [
			'template' => LogItemInterface::TEMPLATE_LOG_ITEM,
			'data' => [
				'title' => $logItem->getTitle(),
				'message' => $logItem->getMessage(),
				'rawLogs' => $logItem->getRawLogs(),
				'dateTime' => $logItem->getDateTimeAsString(),
				'level' => $logItem->getLevel(),
			]
		];
	}

	public function preRenderLogItemComponent(LogItemInterface $logItem): array {
		return [
			'component' => 'CommonLogItem',
			'data' => [
				'title' => $logItem->getTitle(),
				'message' => $logItem->getMessage(),
				'rawLogs' => $logItem->getRawLogs(),
				'dateTime' => $logItem->getDateTimeAsString(),
				'level' => $logItem->getLevel(),
			]
		];
	}

}
