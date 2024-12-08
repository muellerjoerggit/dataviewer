<?php

namespace App\DaViEntity;

interface EntityValidatorInterface {

	/**
	 * validate entity
	 */
	public function validateEntity(EntityInterface $entity, array $options = []): void;

	/**
	 * validate common configurations and requirements of entity type, which don't rely on a single entity
	 */
	public function validateEntityType(string $client, ?EntityInterface $entity = null): void;
}
