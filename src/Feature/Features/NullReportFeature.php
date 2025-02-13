<?php

namespace App\Feature\Features;

use App\DataCollections\Report;
use App\Feature\AbstractFeatureReport;
use App\Feature\FeatureReportInterface;

class NullReportFeature extends AbstractFeatureReport {

	public function getReportList(string $client): array {
		$report = new Report();
		return [$report->setReportHeader('NullReportFeature', 'Fehler: Feature nicht gefunden')];
	}

}
