<?php

namespace App\Services\Export\ExportConfiguration;

interface ExportGroupConfigurationInterface {

  public function getExporterClass(): string;

  public function getKey(): string;

}