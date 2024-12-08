<?php

namespace App\Logger\LogItems;

use App\Logger\LogItemPreRendering\CommonLogItemPreRenderingHandler;
use Exception;
use Symfony\Component\Uid\Uuid;

class LogItem implements LogItemInterface {

  protected string $message;
	protected array $rawLogs = [];
	protected \DateTime $dateTime;
	protected string $level;
	protected string $title;
	protected Uuid $uuid;

    public function __construct(string $message, $title, string $level, \DateTime $dateTime, array $rawLogs = []) {
        $this->message = $message;
		$this->title = $title;
        $this->dateTime = $dateTime;
        $this->level = $level;
		$this->uuid = Uuid::v7();
		if(!empty($rawLogs)) {
			$this->addRawLogs($rawLogs);
		}
    }

	public function getUuidAsString(): string {
		return (string)$this->uuid;
	}

    public static function createLogItem(string $message, $title = '', string $level = LogItemInterface::LOG_LEVEL_INFO, $dateTime = null, $rawLogs = []): LogItemInterface {
        if($dateTime === null) {
            $dateTime = new \DateTime();
        }

        return new static($message, $title, $level, $dateTime, $rawLogs);
    }

	public static function createExceptionLogItem(Exception $exception, $dateTime = null): LogItemInterface {
		if($dateTime === null) {
			$dateTime = new \DateTime();
		}

		$logItem = new static('', 'Fehler DaVi', LogItemInterface::LOG_LEVEL_ERROR, $dateTime, []);
		$logItem->addRawLogs($exception->getMessage());
		return $logItem;
	}

	public static function createEntityNotAvailableLogItem($title = 'Entität nicht verfügbar', $dateTime = null): LogItemInterface {
		if($dateTime === null) {
			$dateTime = new \DateTime();
		}

		return new static('Entität ist in der Datenbank vorhanden, aber als inaktiv/gelöscht markiert', $title, LogItemInterface::LOG_LEVEL_WARNING, $dateTime, []);
	}

    public function getMessage(): string {
        return $this->message;
    }

    public function getDateTimeAsString(): string {
        return $this->dateTime->format('Y-m-d H:i:s');
    }

    public function getLevel(): string {
        return $this->level;
    }

	public function getTitle(): string {
		if(!empty($this->title)) {
			return $this->title;
		}

		if(strlen($this->message) > 80) {
			$title = substr($this->message, 0, 76) . ' ...';
		} else {
			$title = $this->message;
		}

		return $title;
	}

	public static function getType(): string {
		return static::class;
	}

	public function addRawLogs($rawLogs): LogItemInterface {
		if(is_scalar($rawLogs)) {
			$this->rawLogs[] = $rawLogs;
		} elseif(is_array($rawLogs)) {
			$this->rawLogs = array_merge($this->rawLogs, $rawLogs);
		} elseif($rawLogs instanceof Exception) {
			$this->rawLogs[] = $rawLogs->getMessage();
		}

		return $this;
	}

	public function getRawLogs(): array {
		return $this->rawLogs;
	}

	public function getPreRenderingHandler(): string {
		return CommonLogItemPreRenderingHandler::class;
	}
}
