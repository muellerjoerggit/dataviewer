<?php

namespace App\Services\EntityActionHandler;

use App\Services\EntityAction\AbstractEntityActionConfigAttr;
use App\Services\Placeholder\PlaceholderConfigInterface;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class UrlActionConfigAttr extends AbstractEntityActionConfigAttr implements PlaceholderConfigInterface {

  public function __construct(
    string $handler,
    public readonly string $urlTemplate,
    public readonly array $placeholders = [],
    public readonly bool $externalUrl = false,
    string $title = 'Entity action',
    string $description = '',
  ) {
    parent::__construct($handler, $title, $description);
  }

  public function getPlaceholderConfig(): array {
    return $this->placeholders;
  }

  public function getUrlTemplate(): string {
    return $this->urlTemplate;
  }

  public function isValid(): bool {
    return parent::isValid() && !empty($this->urlTemplate);
  }

  public function isExternalUrl(): bool {
    return $this->externalUrl;
  }

}