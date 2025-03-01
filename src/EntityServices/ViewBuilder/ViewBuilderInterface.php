<?php

namespace App\EntityServices\ViewBuilder;

use App\DaViEntity\EntityInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('entity_management.view_builder')]
interface ViewBuilderInterface {

  // extended entity overview options
  public const FORMAT = 'format';

  public const PROPERTIES = 'properties';

  public const ENTITY_LABEL = 'entityLabel';

  public function preRenderEntity(EntityInterface $entity): array;

}
