<?php

namespace App\DaViEntity\Schema;

use App\DaViEntity\EntityInterface;
use App\DaViEntity\Schema\Attribute\EntityTypeDefinition;
use App\Services\AppNamespaces;
use App\Services\DirectoryFileService;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class EntityTypesReader {

  private const string PATTERN_SCHEMA = '/^([a-zA-Z]+Schema).yaml/i';

  private const string PATTERN_ERROR_CODE = '/^([a-zA-Z]+ErrorCodes).yaml/i';

  public const string KEY_SCHEMA_FILE = 'yamlSchema';

  public const string KEY_ERROR_CODE_FILE = 'errorCode';

  public const string KEY_ENTITY_CLASS = 'entityClass';

  public const string KEY_ENTITY_TYPE = 'entityType';

  public function __construct(
    private readonly DirectoryFileService $directoryFileRegister
  ) {}

  public function read(): array {
    $finder = new Finder();
    $finder->directories()->in($this->directoryFileRegister->getEntityTypesDir());

    $ret = [];
    foreach ($finder as $directory) {
      $entityType = $directory->getBasename();
      $files = $this->iterateEntityFiles($directory);

      if (!$this->isValidEntityTypeFiles($files)) {
        continue;
      }

      $ret[$entityType] = $files;
    }

    return $ret;
  }

  private function iterateEntityFiles(SplFileInfo $directory): array {
    $finder = new Finder();
    $finder->files()->in($directory->getRealPath());

    $ret = [];
    $entityType = $directory->getBasename();
    $ret[self::KEY_ENTITY_TYPE] = $entityType;

    foreach ($finder as $file) {
      if ($file->isDir()) {
        continue;
      }

      if (preg_match(self::PATTERN_SCHEMA, $file->getFilename())) {
        $ret[self::KEY_SCHEMA_FILE] = $file;
      } elseif (preg_match(self::PATTERN_ERROR_CODE, $file->getFilename())) {
        $ret[self::KEY_ERROR_CODE_FILE] = $file;
      } elseif ($file->getExtension() == 'php') {
        $className = AppNamespaces::buildNamespace(AppNamespaces::ENTITY_TYPE_NAMESPACE, $entityType, $file->getBasename('.php'));
        $reflection = $this->reflectClass($className);

        if (!$reflection) {
          continue;
        }

        $ret = array_merge($ret, $this->identifyInterface($reflection, $entityType));
      }
    }

    return $ret;
  }

  private function reflectClass($className): ?ReflectionClass {
    try {
      return new ReflectionClass($className);
    } catch (ReflectionException $e) {
      return NULL;
    }
  }

  private function identifyInterface(ReflectionClass $reflectionClass, string $expectedEntityType): array {
    $interfaces = $reflectionClass->getInterfaceNames();
    $entityType = self::getEntityTypeFromReflection($reflectionClass);
    $className = $reflectionClass->getName();
    $key = '';

    if (in_array(EntityInterface::class, $interfaces)) {
      $key = self::KEY_ENTITY_CLASS;
    }

    if (empty($key) || empty($entityType) || $entityType !== $expectedEntityType) {
      return [];
    }

    return [$key => $className];
  }

  public static function getEntityTypeFromReflection(ReflectionClass $reflectionClass): string {
    $attributes = $reflectionClass->getAttributes(EntityTypeDefinition::class);
    return empty($attributes) ? '' : $attributes[0]->getArguments()['name'] ?? '';
  }

  private function isValidEntityTypeFiles(array $files): bool {
    $mandatoryKeys = [self::KEY_SCHEMA_FILE];

    foreach ($mandatoryKeys as $key) {
      if (!isset($files[$key])) {
        return FALSE;
      }
    }

    return TRUE;
  }

}