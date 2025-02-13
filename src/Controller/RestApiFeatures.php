<?php

namespace App\Controller;

use App\DataCollections\Report;
use App\Feature\FeatureLocator;
use App\Feature\FeatureRegister;
use App\Feature\FeatureReportInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RestApiFeatures extends AbstractController {

	#[Route(path: '/api/feature/get/{client}/{featureType}', name: 'app_api_feature_get')]
	public function getFeature(FeatureLocator $featureLocator, string $client,  string $featureType): Response {
		$feature = $featureLocator->getFeature($featureType);
    $response = [];

    if($feature instanceof FeatureReportInterface) {
      $response = $feature->getReportsForResponse($client);
    }

		return $this->json($response);
	}

	#[Route(path: '/api/feature/list/get', name: 'app_api_feature_list')]
	public function getList(FeatureRegister $featureRegister): Response {
		return $this->json($featureRegister->getFeatureList());
	}

}
