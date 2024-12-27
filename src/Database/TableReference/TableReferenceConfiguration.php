<?php

namespace App\Database\TableReference;

use App\Database\TableReferenceHandler\NullTableReferenceHandler;

class TableReferenceConfiguration {

	private array $settings = [];

	public function __construct(
       private readonly string $handler,
       private readonly string $name
    ) {}

  public static function create(string $handler, string $name): TableReferenceConfiguration {
      return new self($handler, $name);
  }

  public static function createNullConfig(string $name): TableReferenceConfiguration {
    return new self(NullTableReferenceHandler::class, $name);
  }

	public function getName(): string {
		return $this->name;
	}

	public function getHandler(): string {
    return $this->handler;
	}

	public function setSettings(array $settings): TableReferenceConfiguration {
		$this->settings = $settings;
		return $this;
	}

	public function getSetting(string $setting, $default = null): mixed {
		return $this->settings[$setting] ?? $default;
	}

  public function getNestedSetting(mixed $default, string ...$settings): mixed {
    $ret = $this->settings;
    foreach ($settings as $setting) {
      if(is_array($ret) && isset($ret[$setting])) {
        $ret = $ret[$setting];
      } else {
        $ret = $default;
        break;
      }
    }

    return $ret;
  }

	public function setSetting(string $setting, mixed $value): TableReferenceConfiguration {
		$this->settings[$setting] = $value;
		return $this;
	}

}
