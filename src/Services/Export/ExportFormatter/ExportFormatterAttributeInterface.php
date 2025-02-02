<?php

namespace App\Services\Export\ExportFormatter;

interface ExportFormatterAttributeInterface {

  public function getExportFormatterClass(): string;

}