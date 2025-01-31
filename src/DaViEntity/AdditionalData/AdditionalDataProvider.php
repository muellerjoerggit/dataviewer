<?php

namespace App\DaViEntity\AdditionalData;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class AdditionalDataProvider {

  public const string CLASS_PROPERTY = 'additionalDataProviders';

  public function __construct(
    public readonly array $additionalDataProviders,
  ) {}

}