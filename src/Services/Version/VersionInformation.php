<?php

namespace App\Services\Version;

class VersionInformation {

  public const int TYPE_ALL_VERSIONS = 0;
  public const int TYPE_SINCE_VERSION = 1;

  /**
   * @param string $version
   * @param int $type
   */
  public function __construct(
    private readonly string $version,
    private readonly int $type
  ) {}

  public function getVersion(): string {
    return $this->version;
  }

  public function getType(): int {
    return $this->type;
  }

  public function isTypeSince(): bool {
    return $this->getType() === self::TYPE_SINCE_VERSION;
  }

}