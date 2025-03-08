<?php

namespace App\Services;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Yaml\Yaml;

/**
 * central service for all files and directories
 */
class DirectoryFileService {

  public function __construct(
    #[Autowire('%kernel.project_dir%')] private $rootDir,
    private readonly ParameterBagInterface $parameterBag,
  ) {}

  public function getSrcDir(): string {
    return $this->getRootDir() . '/src';
  }

  public function getEntityTypesDir(): string {
    return $this->getSrcDir() . '/EntityTypes';
  }

  public function getRootDir(): string {
    return $this->rootDir;
  }

  public function parseYamlFromFile(string $file): array {
    if (!$this->fileExists($file)) {
      return [];
    }

    return Yaml::parseFile($file);
  }

  public function fileExists(string $file): bool {
    return file_exists($file);
  }

  public function getCommonErrorCodes(): array {
    return $this->parseYamlFromFile($this->getSrcDir() . '/Services/Validation/CommonErrorCodes.yaml');
  }

  public function getTaskCommandDir(): string {
    return $this->getSrcDir() . '/Services/BackgroundTaskCommands';
  }

  public function getFeatureDir(): string {
    return $this->getSrcDir() . '/Feature/Features';
  }

  public function getPhpExecutable(): string {
    return (new PhpExecutableFinder())->find();
  }

  public function getConsoleDir(): string {
    return $this->getRootDir() . '/bin/console';
  }

  public function getTempDir(): string {
    return sys_get_temp_dir();
  }

  public function getTempFileName(int $fileType): string {
    $prefix = match($fileType) {
      FileService::FILE_TYPE_EXPORT => 'exp_',
      default => 'tmp_',
    };

    return (new Filesystem())->tempnam($this->getTempDir(), $prefix);
  }

  public function getLogDir(): string {
    try {
      $logDir = $this->parameterBag->get('kernel.logs_dir');
    } catch (ParameterNotFoundException $exception) {
      $logDir = '';
    }

    return $logDir;
  }

}
