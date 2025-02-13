<?php

namespace App\Feature;

use App\Feature\Features\NullReportFeature;
use App\Services\AbstractLocator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class FeatureLocator extends AbstractLocator {

	public function __construct(
    private readonly FeatureRegister $featureRegister,
    #[AutowireLocator('feature')]
    ServiceLocator $services
  ) {
		parent::__construct($services);
	}

	public function getFeature(string $feature): FeatureInterface {
		$featureClass = $this->featureRegister->getFeatureClass($feature);

		if($this->has($featureClass)) {
			return $this->get($featureClass);
		} else {
			return $this->get(NullReportFeature::class);
		}
	}

}
