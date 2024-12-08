<?php

namespace App\Logger;

use App\Logger\LogItems\LogItem;
use App\Logger\LogItems\LogItemInterface;
use Symfony\Component\Uid\Uuid;

class Logger {

	private array $logs = [];
	private array $loggingContainer = [];

	public function addLog(string | array | LogItemInterface $logs): string {
		if(is_array($logs)) {
			foreach ($logs as $log) {
				$this->addLogInternal($log);
			}
		} elseif (is_string($logs) || $logs instanceof LogItemInterface) {
			$item = $this->addLogInternal($logs);
			return !$item ? '' : $item;
		}

		return '';
	}

	private function addLogInternal($log): string | bool {
		if(is_string($log)) {
			$log = $this->createLogItemFromString($log);
		}

		if($log instanceof LogItemInterface) {
			return $this->addLogItemInternal($log);
		}

		return false;
	}

	private function addLogItemInternal(LogItemInterface $logItem): string {
		$uuid = $logItem->getUuidAsString();
		$this->logs[$uuid] = $logItem;
		$this->addToLoggingLists($uuid);
		return $uuid;
	}

	private function createLogItemFromString(string $logMessage): LogItemInterface {
		return LogItem::createLogItem($logMessage);
	}

	private function addToLoggingLists(string $uuid): void {
		foreach ($this->loggingContainer as $uuidContainer => $logs) {
			$this->loggingContainer[$uuidContainer][] = $uuid;
		}
	}

	public function startLoggingContainer(): string {
		$uuid = (string)Uuid::v7();
		$this->loggingContainer[$uuid] = [];
		return $uuid;
	}

	public function endLoggingContainer(string $uuidContainer, $returnLogItems = true): array {
		$uuids = $this->loggingContainer[$uuidContainer] ?? [];
		unset($this->loggingContainer[$uuidContainer]);

		if($returnLogItems) {
			return array_intersect_key($this->logs, array_flip($uuids));
		}

		return $uuids;
	}

	public function getLoggingContainer(string $uuid): array {
		return $this->loggingContainer[$uuid] ?? [];
	}

	public function getLog(string | array $logUuids): array {
		if(is_string($logUuids)) {
			return $this->logs[$logUuids] ?? [];
		}

		if(is_array($logUuids)) {
			return array_intersect_key($this->logs, array_flip($logUuids));
		}

		return [];
	}

	public function iterateLogs(array $logIndex = []): \Generator {
		if(empty($logIndex)) {
			foreach ($this->logs as $index => $logItem) {
				if(!($logItem instanceof LogItemInterface)) {
					continue;
				}
				yield $index => $logItem;
			}
		} else {
			$logs = $this->getLog($logIndex);
			foreach ($logs as $index => $logItem) {
				if(!($logItem instanceof LogItemInterface)) {
					continue;
				}
				yield $index => $logItem;
			}
		}
	}

	public function logBacktrace(): LogItemInterface {
		$logItem = LogItem::createLogItem('', 'Stacktrace', LogItemInterface::LOG_LEVEL_DAVI_DEBUG);
		$backtraceArray = debug_backtrace();
		$backtrace = '';
		foreach ($backtraceArray as $trace) {
			$class = $trace['class'] ?? $trace['file'];
			$backtrace .= $class . '(' . $trace['line'] . ')::' . $trace['function'] .  " \n";
		}
		$logItem->addRawLogs($backtrace);
		$this->addLog($logItem);
		return $logItem;
	}

}
