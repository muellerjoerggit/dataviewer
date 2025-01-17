<?php

namespace App\DaViEntity\Schema;

use App\DaViEntity\Schema\Attribute\DatabaseAttr;
use App\DaViEntity\Schema\Attribute\EntityOverviewSchemaAttr as EntityOverviewClass;
use App\DaViEntity\Schema\Attribute\ExtendedEntityOverviewSchemaAttr as ExtendedEntityOverviewClass;
use App\DaViEntity\Schema\Attribute\EntityTypeAttr;
use App\Item\Property\Attribute\EntityOverviewPropertyAttr;
use App\Item\Property\Attribute\ExtendedEntityOverviewPropertyAttr;
use App\Item\Property\Attribute\LabelPropertyAttr as LabelPropProperty;
use App\DaViEntity\Schema\Attribute\LabelPropertySchemaAttr as LabelPropClass;
use App\Item\Property\Attribute\SearchPropertyAttr;
use App\Item\Property\Attribute\UniquePropertyAttr;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;

class EntitySchemaBuilderFromAttributes {

  public function buildSchema(string $entityClass): EntitySchemaInterface | null {
    $reflection = $this->reflect($entityClass);

    if(!$reflection) {
      return null;
    }

    $schema = new EntitySchema();

    if(!$this->fillBasics($reflection, $schema)) {
      return null;
    }

    if(!$this->fillSpecialProperties($reflection, $schema)) {
      return null;
    }

    $this->fillDatabase($reflection, $schema);

    return $schema;
  }

  private function reflect(string $entityClass): ReflectionClass | null {
    try {
      return new ReflectionClass($entityClass);
    } catch (\ReflectionException $exception) {
      return null;
    }
  }

  private function fillBasics(ReflectionClass $reflection, EntitySchemaInterface $schema): bool {
    $entityTypeAttr = $reflection->getAttributes(EntityTypeAttr::class);
    $entityTypeAttr = reset($entityTypeAttr);

    if(!$entityTypeAttr instanceof ReflectionAttribute) {
      return false;
    }

    $entityTypeAttr = $entityTypeAttr->newInstance();

    $schema
      ->setEntityType($entityTypeAttr->name)
      ->setEntityLabel($entityTypeAttr->getLabel());

    return true;
  }

  private function fillSpecialProperties(ReflectionClass $reflection, EntitySchemaInterface $schema): bool {
    $uniqueProp = [];
    $labelTemp = [];
    $searchProps = [];
    $entityOverview = [];
    $extendedEntityOverview = [];
    foreach($reflection->getProperties() as $property) {
      $propertyName = $property->getName();
      $uniquePropertyAttr = $property->getAttributes(UniquePropertyAttr::class);
      $uniquePropertyAttr = reset($uniquePropertyAttr);
      if($uniquePropertyAttr instanceof ReflectionAttribute) {
        $uniqueProp[$uniquePropertyAttr->getArguments()['name']][] = $propertyName;
      }

      $this->processPropertyAttribute($property, LabelPropProperty::class, $labelTemp);
      $this->processPropertyAttribute($property, EntityOverviewPropertyAttr::class, $entityOverview);
      $this->processPropertyAttribute($property, ExtendedEntityOverviewPropertyAttr::class, $extendedEntityOverview);

      $searchPropertyAttr = $property->getAttributes(SearchPropertyAttr::class);
      $searchPropertyAttr = reset($searchPropertyAttr);
      if($searchPropertyAttr instanceof ReflectionAttribute) {
        $searchProps[] = $propertyName;
      }
    }

    $this->processClassAttribute($reflection, LabelPropClass::class, $labelTemp);
    $this->processClassAttribute($reflection, EntityOverviewClass::class, $entityOverview);
    $this->processClassAttribute($reflection, ExtendedEntityOverviewClass::class, $extendedEntityOverview);


    if(empty($uniqueProp)) {
      return false;
    }

    $labelProp = [];
    if(!empty($labelTemp)) {
      $labelProp = $this->sortProperties($labelTemp);
    } else {
      $firstUniqueProp = reset($uniqueProp);
      foreach ($firstUniqueProp as $value) {
        $labelProp[$value] = '';
      }
    }

    $entityOverview = $this->sortProperties($entityOverview);
    $extendedEntityOverview = $this->sortProperties($extendedEntityOverview);

    $schema->setUniqueProperties($uniqueProp);
    $schema->setEntityLabelProperties($labelProp);
    $schema->setSearchProperties(empty($searchProps) ? array_keys($labelProp) : $searchProps);
    $schema->setExtendedEntityOverviewProperties($extendedEntityOverview);
    $schema->setEntityOverviewProperties($entityOverview);
    return true;
  }

  private function processPropertyAttribute(ReflectionProperty $property, string $attrClass, array &$result): void {
    $propertyAttr = $property->getAttributes($attrClass);
    $propertyAttr = reset($propertyAttr);
    if($propertyAttr instanceof ReflectionAttribute) {
      $labelArgs = $propertyAttr->getArguments();

      $result[] = [
        'name' => $property->getName(),
        'label' => $labelArgs['label'] ?? '',
        'rank' => $labelArgs['rank'] ?? 0,
      ];
    }
  }

  private function processClassAttribute(ReflectionClass $reflection, string $attrClass, array &$result): void {
    $classAttributes = $reflection->getAttributes($attrClass);

    foreach ($classAttributes as $attribute) {
      $args = $attribute->getArguments();
      $path = $args['path'] ?? '';

      if(empty($path)) {
        continue;
      }

      $result[] = [
        'name' => $path,
        'label' => $args['label'] ?? '',
        'rank' => $args['rank'] ?? 0,
      ];
    }
  }

  private function sortProperties(array $properties): array {
    $ret = [];
    usort($properties, function($a, $b) {
      return $a['rank'] < $b['rank'] ? -1 : 1;
    });
    foreach($properties as $property) {
      $ret[$property['name']] = $property['label'];
    }

    return $ret;
  }

  private function fillDatabase(ReflectionClass $reflection, EntitySchemaInterface $schema): void {
    $attr = $reflection->getAttributes(DatabaseAttr::class);
    $attr = reset($attr);

    if(!$attr instanceof ReflectionAttribute) {
      return;
    }

    $attr = $attr->newInstance();

    if(!$attr instanceof DatabaseAttr || !$attr->isValid()) {
      return;
    }

    $schema->setDatabase($attr->databaseClass);
    $schema->setBaseTable($attr->baseTable);
  }

}