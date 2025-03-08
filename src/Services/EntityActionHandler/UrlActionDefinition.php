<?php

namespace App\Services\EntityActionHandler;

use App\Services\EntityAction\AbstractEntityActionDefinition;
use App\Services\Placeholder\PlaceholderDefinitionInterface;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class UrlActionDefinition extends AbstractEntityActionDefinition implements PlaceholderDefinitionInterface {

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

  public function getPlaceholderDefinition(): array {
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