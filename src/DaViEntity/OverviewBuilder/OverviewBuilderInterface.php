<?php

namespace App\DaViEntity\OverviewBuilder;

use App\DaViEntity\EntityInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('entity_management.overview_builder')]
interface OverviewBuilderInterface {

  // extended entity overview options
  public const FORMAT = 'format';

  public const PROPERTIES = 'properties';

  public const ENTITY_LABEL = 'entityLabel';

  public function buildEntityOverview(EntityInterface $entity, array $options = []): array;

  public function buildExtendedEntityOverview(EntityInterface $entity, array $options = []): array;

}
