<?php

namespace App\Development\Controller;

use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\EntityTypes\User\UserEntity;
use App\Services\DirectoryFileRegister;
use App\Services\Export\CsvExport;
use App\Services\Export\ExportConfiguration\ExportConfiguration;
use App\Services\Export\ExportConfiguration\ExportEntityPathConfiguration;
use App\Services\Export\ExportConfiguration\ExportPropertyConfig;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;

class Export extends AbstractController {

  public function export(CsvExport $csvExport, EntityTypeSchemaRegister $schemaRegister, DirectoryFileRegister $fileRegister): Response {
    $schema = $schemaRegister->getEntityTypeSchema('User');
    $pathConfigUser = new ExportEntityPathConfiguration();

    foreach (['usr_id', 'firstname', 'lastname', 'email', 'roles'] as $property) {
      $config = new ExportPropertyConfig(
        $property,
        $schema->getProperty($property),
        []
      );
      $pathConfigUser->addPropertyConfig($config);
    }

    $schemaRole = $schemaRegister->getEntityTypeSchema('Role');
    $pathConfigRole = new ExportEntityPathConfiguration(['roles']);

    foreach (['rol_id', 'title'] as $property) {
      $config = new ExportPropertyConfig(
        $property,
        $schemaRole->getProperty($property),
        []
      );
      $pathConfigRole->addPropertyConfig($config);
    }

    $exportConfig = new ExportConfiguration('umbrella', UserEntity::class);
    $exportConfig->addEntityPath($pathConfigUser)->addEntityPath($pathConfigRole);

    $csv = $csvExport->export($exportConfig);

    $tempDir = $fileRegister->getTempDir();
    $tmpFileName = (new Filesystem())->tempnam($tempDir, 'sb_', '.csv');
    $tmpFile = fopen($tmpFileName, 'wb+');
    if (!\is_resource($tmpFile)) {
      throw new \RuntimeException('Unable to create a temporary file.');
    }

    $csv = iconv("UTF-8", "Windows-1252//IGNORE", $csv);

    file_put_contents($tmpFileName, $csv);

    fclose($tmpFile);

    return $this->file($tmpFileName);
  }

}