<?php

namespace App\DaViEntity;

interface EntityViewBuilderInterface {

	// extended entity overview types
	public const EXT_OVERVIEW_SCALAR = 1;
	public const EXT_OVERVIEW_REFERENCE = 2;
	public const EXT_OVERVIEW_HTML = 3;
	public const EXT_OVERVIEW_JSON = 4;
	public const EXT_OVERVIEW_VALIDATION = 5;
	public const EXT_OVERVIEW_ADDITIONAL = 6;

	// extended entity overview options
	public const FORMAT = 'format';
	public const PROPERTIES = 'properties';
	public const ENTITY_LABEL = 'entityLabel';

  public function preRenderEntity(EntityInterface $entity): array;

  public function buildEntityOverview(EntityInterface $entity, array $options = []): array;

  public function buildExtendedEntityOverview(EntityInterface $entity, array $options = []): array;

}
