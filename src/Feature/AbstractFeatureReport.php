<?php

namespace App\Feature;

use App\DataCollections\Report;

abstract class AbstractFeatureReport implements FeatureReportInterface {

	protected FeatureReader $featureRegister;

	public function __construct(FeatureReader $featureRegister) {
		$this->featureRegister = $featureRegister;
	}

	abstract public function getReportList(string $client): array;

  public function getReportsForResponse(string $client): array {
    $reports = $this->getReportList($client);
    $reportArray = [];

    foreach ($reports as $report) {
      if(!($report instanceof Report)) {
        continue;
      }

      $reportArray[] = $report->getAsArray();
    }

    return [
      'featureType' => 'reports',
      'data' => $reportArray,
    ];
  }
}
