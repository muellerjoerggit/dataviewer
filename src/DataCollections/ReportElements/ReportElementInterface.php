<?php

namespace App\DataCollections\ReportElements;

interface ReportElementInterface {

	public function getElementData(): array;

  public function isValid(): bool;

}
