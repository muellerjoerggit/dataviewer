<?php

namespace App\DaViEntity\AdditionalData;

use App\DaViEntity\EntityTypeAttributesReader;
use App\Services\AbstractLocator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class AdditionalDataProviderLocator extends AbstractLocator {

  public function __construct(
    private readonly EntityTypeAttributesReader $entityTypeAttributesReader,
    #[AutowireLocator('entity_management.additional_data_provider')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  /**
   * @return array<\App\DaViEntity\AdditionalData\AdditionalDataProviderInterface>
   */
  public function getAdditionalDataProviders(string $entityClass): array {
    $additionalDataProviders = $this->entityTypeAttributesReader->getAdditionalDataProviderClassList($entityClass);

    $ret = [];
    foreach ($additionalDataProviders as $additionalDataProvider) {
      if ($this->has($additionalDataProvider)) {
        $ret[] = $this->get($additionalDataProvider);
      }
    }
    return $ret;
  }

}