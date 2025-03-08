<?php

namespace App\Logger\LogItems;

use App\Logger\LogItemPreRendering\CommonLogItemPreRenderingHandler;
use App\Logger\LogLevels;
use DateTime;
use Exception;
use Symfony\Component\Uid\Uuid;

class LogItem implements LogItemInterface {

  protected string $message = '';

  protected array $rawLogs = [];

  protected DateTime $dateTime;

  protected string $level = '';

  protected string $title = '';

  protected Uuid $uuid;

  public function __construct(string $message, string $title, string $level, DateTime $dateTime, array $rawLogs = []) {
    $this->message = $message;
    $this->title = $title;
    $this->dateTime = $dateTime;
    $this->level = $level;
    $this->uuid = Uuid::v7();
    if (!empty($rawLogs)) {
      $this->addRawLogs($rawLogs);
    }
  }

  public function addRawLogs($rawLogs): LogItemInterface {
    if (is_scalar($rawLogs)) {
      $this->rawLogs[] = $rawLogs;
    } elseif (is_array($rawLogs)) {
      $this->rawLogs = array_merge($this->rawLogs, $rawLogs);
    } elseif ($rawLogs instanceof Exception) {
      $this->rawLogs[] = $rawLogs->getMessage();
    }

    return $this;
  }

  public function getMessage(): string {
    return $this->message;
  }

  public static function createLogItem(string $message, $title = '', string $level = LogLevels::INFO, $dateTime = NULL, $rawLogs = []): LogItemInterface {
    if ($dateTime === NULL) {
      $dateTime = new DateTime();
    }

    return new static($message, $title, $level, $dateTime, $rawLogs);
  }

  public static function createAvailabilityLogItem(): LogItemInterface {
    return new static('Entity is not available anymore', 'Not available', LogLevels::WARNING, new DateTime());
  }

  public static function createExceptionLogItem(Exception $exception, $dateTime = NULL): LogItemInterface {
    if ($dateTime === NULL) {
      $dateTime = new DateTime();
    }

    $logItem = new static('', 'Fehler DaVi', LogLevels::ERROR, $dateTime, []);
    $logItem->addRawLogs($exception->getMessage());
    return $logItem;
  }

  public static function createEntityNotAvailableLogItem($title = 'Entität nicht verfügbar', $dateTime = NULL): LogItemInterface {
    if ($dateTime === NULL) {
      $dateTime = new DateTime();
    }

    return new static('Entität ist in der Datenbank vorhanden, aber als inaktiv/gelöscht markiert', $title, LogLevels::WARNING, $dateTime, []);
  }

  public static function getType(): string {
    return static::class;
  }

  public function getUuidAsString(): string {
    return (string) $this->uuid;
  }

  public function getDateTimeAsString(): string {
    return $this->dateTime->format('Y-m-d H:i:s');
  }

  public function getLevel(): string {
    return $this->level;
  }

  public function getTitle(): string {
    if (!empty($this->title)) {
      return $this->title;
    }

    if (strlen($this->message) > 80) {
      $title = substr($this->message, 0, 76) . ' ...';
    } else {
      $title = $this->message;
    }

    return $title;
  }

  public function getRawLogs(): array {
    return $this->rawLogs;
  }

  public function getPreRenderingHandler(): string {
    return CommonLogItemPreRenderingHandler::class;
  }

}
