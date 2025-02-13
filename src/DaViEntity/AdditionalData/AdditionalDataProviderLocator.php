<?php

namespace App\DaViEntity\AdditionalData;

use App\DaViEntity\EntityTypeAttributesReader;
use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\Services\AbstractLocator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class AdditionalDataProviderLocator extends AbstractLocator {

  public function __construct(
    private readonly EntityTypeSchemaRegister $entityTypeSchemaRegister,
    #[AutowireLocator('entity_management.additional_data_provider')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  /**
   * @return AdditionalDataProviderInterface[]
   */
  public function getAdditionalDataProviders(string | EntitySchema $entitySchema, string $version): array {
    if(is_string($entitySchema)) {
      $entitySchema = $this->entityTypeSchemaRegister->getSchemaFromEntityClass($entitySchema);
    }

    $ret = [];
    foreach ($entitySchema->iterateAdditionalDataProviderDefinitions($version) as $additionalDataProviderDefinition) {
      if ($this->has($additionalDataProviderDefinition->getAdditionalDataProviderClass())) {
        $ret[] = $this->get($additionalDataProviderDefinition);
      }
    }
    return $ret;
  }

}