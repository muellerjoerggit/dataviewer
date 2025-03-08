<?php

namespace App\Feature;

use App\Services\AppNamespaces;
use App\Services\DirectoryFileService;
use Exception;
use Iterator;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use EmptyIterator;

class FeatureReader {

  private const string PATTERN_PHP_FILE = '/^([a-zA-Z]+)Feature.php/i';

	public function __construct(
		private readonly DirectoryFileService $directoryFileRegister,
	){}

  private function reflectClass($className): ReflectionClass | null {

    try {
      $reflection = new ReflectionClass($className);
    } catch (ReflectionException $e) {
      return null;
    }

    return $reflection;
  }

	public function getFeatures(): array {
    $ret = [];
		foreach($this->getPhpFiles($this->directoryFileRegister->getFeatureDir()) as $file) {
			if(!($file instanceof SplFileInfo)) {
				continue;
			}

      $feature = $this->readFeature($file);

      if(empty($feature)) {
        continue;
      }

      $ret[$feature['feature']] = $feature;
		}
    return $ret;
	}

  private function readFeature(SplFileInfo $file): array {
    if(preg_match(self::PATTERN_PHP_FILE, $file->getFilename(), $matches)) {
      $shortName = $matches[1];
      $classNamespace = AppNamespaces::buildNamespace(AppNamespaces::NAMESPACE_FEATURES, $shortName . 'Feature');
      $reflection = $this->reflectClass($classNamespace);

      if(!($reflection instanceof ReflectionClass)) {
        return [];
      }

      if(!array_key_exists(FeatureInterface::class, $reflection->getInterfaces())) {
        return [];
      }

      $attributes = $reflection->getAttributes(FeatureDefinition::class);
      $attribute = reset($attributes);

      if(!$attribute) {
        return [];
      }

      $attribute = $attribute->newInstance();

      return [
        'feature' => $shortName,
        'class' => $classNamespace,
        'label' => $attribute->getLabel() ?? $shortName,
        'description' => $attribute->getDescription(),
      ];
    }

    return [];
  }

  protected function getPhpFiles($dir): Iterator {
    try {
      $finder = new Finder();
      $finder->files()->in($dir);
      if($finder->hasResults()) {
        return $finder->files()->name(['*.php'])->getIterator();
      }
    } catch (DirectoryNotFoundException | Exception $exception) {

    }

    return new EmptyIterator();
  }
}
