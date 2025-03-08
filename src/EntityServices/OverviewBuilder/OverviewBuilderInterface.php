<?php

namespace App\EntityServices\OverviewBuilder;

use App\DaViEntity\EntityInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('entity_management.overview_builder')]
interface OverviewBuilderInterface {

  // extended entity overview options
  public const string FORMAT = 'format';

  public const string PROPERTIES = 'properties';

  public const string ENTITY_LABEL = 'entityLabel';

  public function buildEntityOverview(EntityInterface $entity, array $options = []): array;

  public function buildExtendedEntityOverview(EntityInterface $entity, array $options = []): array;

}
