<?php

namespace App\Services;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;

/**
 * central service for all files and directories
 */
class DirectoryFileRegister {

  public function __construct(
    #[Autowire('%kernel.project_dir%')] private $rootDir
  ) {}

  public function getEntityTypesDir(): string {
    return $this->getSrcDir() . '/DaViEntity/EntityTypes';
  }

  public function getRootDir(): string {
    return $this->rootDir;
  }

  public function getSrcDir(): string {
    return $this->getRootDir() . '/src';
  }

  public function fileExists(string $file): bool {
    return file_exists($file);
  }

  public function parseYamlFromFile(string $file): array {
    if(!$this->fileExists($file)) {
      return [];
    }

    return Yaml::parseFile($file);
  }

  public function getPreDefinedPropertyConfiguration(): array {
    $file = $this->getSrcDir() . '/Item/Property/preDefinedPropertyConfiguration.yaml';
    return $this->parseYamlFromFile($file);
  }

  public function getCommonErrorCodes(): array {
    return $this->parseYamlFromFile($this->getSrcDir() . '/Services/Validation/CommonErrorCodes.yaml');
  }

}
