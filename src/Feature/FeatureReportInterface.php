<?php

namespace App\Feature;

use App\DataCollections\Report;

interface FeatureReportInterface extends FeatureInterface {

	/**
	 * @return Report[]
	 */
	public function getReportList(string $client): array;

  public function getReportsForResponse(string $client): array;

}
