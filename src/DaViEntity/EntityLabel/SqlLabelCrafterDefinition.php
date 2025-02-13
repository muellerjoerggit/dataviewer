<?php

namespace App\DaViEntity\EntityLabel;

use App\Services\Version\VersionList;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class SqlLabelCrafterDefinition implements LabelCrafterDefinitionInterface {

  public const string CLASS_PROPERTY = 'sqlEntityLabelCrafterClass';
  private VersionList $versionList;

  public function __construct(
    public readonly string $sqlEntityLabelCrafterClass,
    public readonly string $since_version = '',
    public readonly string $until_version = '',
  ) {}

  public function setVersionList(VersionList $versionList): SqlLabelCrafterDefinition {
    $this->versionList = $versionList;
    return $this;
  }

  public function getVersionKey(): string {
    if(empty($this->since_version)) {
      return '';
    }
    $key = 'since_' . $this->since_version;
    return !empty($this->until_version) ? $key . '_until_' . $this->until_version : $key;
  }

  public function isValid(): bool {
    return !empty($this->sqlEntityLabelCrafterClass) && isset($this->versionList);
  }
}