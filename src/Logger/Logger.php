<?php

namespace App\Logger;

use App\Logger\LogItems\LogItem;
use App\Logger\LogItems\LogItemInterface;
use Generator;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;
use Stringable;

class Logger {

  private array $logs = [];
  private array $loggingContainer = [];

  public function __construct(
    private readonly LoggerInterface $logger,
  ) {}

  public function startLoggingContainer(): string {
    $uuid = (string) Uuid::v7();
    $this->loggingContainer[$uuid] = [];
    return $uuid;
  }

  public function endLoggingContainer(string $uuidContainer, $returnLogItems = TRUE): array {
    $uuids = $this->loggingContainer[$uuidContainer] ?? [];
    unset($this->loggingContainer[$uuidContainer]);

    if ($returnLogItems) {
      return array_intersect_key($this->logs, array_flip($uuids));
    }

    return $uuids;
  }

  public function getLoggingContainer(string $uuid): array {
    return $this->loggingContainer[$uuid] ?? [];
  }

  public function iterateLogs(array $logIndex = []): Generator {
    if (empty($logIndex)) {
      foreach ($this->logs as $index => $logItem) {
        if (!($logItem instanceof LogItemInterface)) {
          continue;
        }
        yield $index => $logItem;
      }
    } else {
      $logs = $this->getLog($logIndex);
      foreach ($logs as $index => $logItem) {
        if (!($logItem instanceof LogItemInterface)) {
          continue;
        }
        yield $index => $logItem;
      }
    }
  }

  public function getLog(string|array $logUuids): array {
    if (is_string($logUuids)) {
      return $this->logs[$logUuids] ?? [];
    }

    if (is_array($logUuids)) {
      return array_intersect_key($this->logs, array_flip($logUuids));
    }

    return [];
  }

  public function logBacktrace(): LogItemInterface {
    $logItem = LogItem::createLogItem('', 'Stacktrace', LogItemInterface::LOG_LEVEL_INTERNAL_DEBUG);
    $backtraceArray = debug_backtrace();
    $backtrace = '';
    foreach ($backtraceArray as $trace) {
      $class = $trace['class'] ?? $trace['file'];
      $backtrace .= $class . '(' . $trace['line'] . ')::' . $trace['function'] . " \n";
    }
    $logItem->addRawLogs($backtrace);
    $this->addLog($logItem);
    return $logItem;
  }

  public function addLog(string|array|LogItemInterface $logs): string {
    if (is_array($logs)) {
      foreach ($logs as $log) {
        $this->addLogInternal($log);
      }
    } elseif (is_string($logs) || $logs instanceof LogItemInterface) {
      $item = $this->addLogInternal($logs);
      return !$item ? '' : $item;
    }

    return '';
  }

  private function addLogInternal($log): string|bool {
    if (is_string($log)) {
      $log = $this->createLogItemFromString($log);
    }

    if ($log instanceof LogItemInterface) {
      return $this->addLogItemInternal($log);
    }

    return FALSE;
  }

  private function createLogItemFromString(string $logMessage): LogItemInterface {
    return LogItem::createLogItem($logMessage);
  }

  private function addLogItemInternal(LogItemInterface $logItem): string {
    $uuid = $logItem->getUuidAsString();
    $this->logs[$uuid] = $logItem;
    $this->addToLoggingLists($uuid);
    return $uuid;
  }

  private function addToLoggingLists(string $uuid): void {
    foreach ($this->loggingContainer as $uuidContainer => $logs) {
      $this->loggingContainer[$uuidContainer][] = $uuid;
    }
  }

  public function emergency(string|Stringable $message, array $context = []): void {
    $this->logger->emergency($message, $context);
  }

  public function alert(string|\Stringable $message, array $context = []): void {
    $this->logger->alert($message, $context);
  }

  public function critical(string|\Stringable $message, array $context = []): void {
    $this->logger->critical($message, $context);
  }

  public function error(string|\Stringable $message, array $context = []): void {
    $this->logger->error($message, $context);
  }

  public function warning(string|\Stringable $message, array $context = []): void {
    $this->logger->warning($message, $context);
  }

  public function notice(string|\Stringable $message, array $context = []): void {
    $this->logger->notice($message, $context);
  }

  public function info(string|\Stringable $message, array $context = []): void {
    $this->logger->info($message, $context);
  }

  public function debug(string|\Stringable $message, array $context = []): void {
    $this->logger->debug($message, $context);
  }

  public function log($level, string|\Stringable  $message, array $context = []): void {
    $this->logger->log($level, $message, $context);
  }

}
