<?php

namespace App\Database\TableReference;

use App\Database\TableReferenceHandler\NullTableReferenceHandler;

class TableReferenceConfiguration {

	private array $settings = [];

	public function __construct(
    private readonly string $handler,
    private readonly string $name,
    private readonly string $fromEntityType
  ) {}

  public static function create(string $handler, string $name, string $fromEntityType ): TableReferenceConfiguration {
      return new self($handler, $name, $fromEntityType);
  }

  public static function createNullConfig(string $name, string $fromEntityType ): TableReferenceConfiguration {
    return new self(NullTableReferenceHandler::class, $name, $fromEntityType);
  }

  public function getFromEntityType(): string {
    return $this->fromEntityType;
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
