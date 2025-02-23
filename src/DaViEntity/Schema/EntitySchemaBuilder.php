<?php

namespace App\DaViEntity\Schema;

use App\Database\Aggregation\AggregationConfigurationBuilder;
use App\Database\BaseQuery\BaseQueryDefinitionInterface;
use App\Database\TableReferenceHandler\Attribute\TableReferenceDefinitionInterface;
use App\DaViEntity\AdditionalData\AdditionalDataProviderDefinitionInterface;
use App\DaViEntity\ColumnBuilder\ColumnBuilderDefinitionInterface;
use App\DaViEntity\Creator\CreatorDefinitionInterface;
use App\DaViEntity\DataProvider\DataProviderDefinitionInterface;
use App\DaViEntity\EntityTypeAttributesReader;
use App\DaViEntity\ListProvider\ListProviderDefinitionInterface;
use App\DaViEntity\OverviewBuilder\OverviewBuilderDefinitionInterface;
use App\DaViEntity\Refiner\RefinerDefinitionInterface;
use App\DaViEntity\Repository\RepositoryDefinitionInterface;
use App\DaViEntity\Schema\Attribute\DatabaseDefinition;
use App\DaViEntity\Schema\Attribute\EntityTypeAttr;
use App\DaViEntity\SimpleSearch\SimpleSearchDefinitionInterface;
use App\DaViEntity\ViewBuilder\ViewBuilderDefinitionInterface;
use App\Item\Property\PropertyAttributesReader;
use App\Item\Property\PropertyConfigurationBuilder;
use App\Services\Version\VersionListInterface;
use App\Services\Version\VersionService;
use App\Services\Version\VersionInformationWrapperInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Finder\SplFileInfo;

class EntitySchemaBuilder {

  private const string YAML_PARAM_AGGREGATIONS = 'aggregations';

  public function __construct(
    private readonly PropertyConfigurationBuilder $propertyConfigurationBuilder,
    private readonly AggregationConfigurationBuilder $aggregationConfigurationBuilder,
    private readonly EntityTypeAttributesReader $attributesReader,
    private readonly PropertyAttributesReader $propertyAttributesReader,
    private readonly VersionService $versionService,
  ) {}

  public function buildSchema(SplFileInfo $file, string $entityClass): EntitySchemaInterface | null {
    $attributesContainer = $this->attributesReader->buildSchemaAttributesContainer($entityClass);
    $this->propertyAttributesReader->appendPropertyAttributesContainer($attributesContainer, $entityClass);
    $yaml = Yaml::parseFile($file->getRealPath());
    $schema = new EntitySchema($entityClass);

    if(!$attributesContainer->isValid()) {
      return null;
    }

    if(!$this->fillSchemaBasics($schema, $attributesContainer)) {
      return null;
    }
    $this->fillDatabase($schema, $attributesContainer);
    $this->fillDatabaseDetails($schema, $attributesContainer);
    $this->fillProperties($schema, $attributesContainer);

    if($attributesContainer->hasSqlFilterDefinitions()) {
      $this->buildFilters($schema, $attributesContainer);
    }

    if(isset($yaml[self::YAML_PARAM_AGGREGATIONS])) {
      $this->buildAggregations($schema,  $yaml);
    }

    $this->fillEntityServices($schema, $attributesContainer);
    $this->fillUniqueProperties($schema, $attributesContainer);
    $this->fillLabelProperties($schema, $attributesContainer);
    $this->fillSearchProperties($schema, $attributesContainer);
    $this->fillEntityOverview($schema, $attributesContainer);
    $this->fillExtendedEntityOverview($schema, $attributesContainer);

    if($attributesContainer->hasEntityActions()){
      $this->fillEntityActions($schema, $attributesContainer);
    }

    return $schema;
  }

  private function fillEntityActions(EntitySchemaInterface $schema, SchemaDefinitionsContainer $container): void {
    foreach($container->iterateEntityActionConfigAttributes() as $attribute) {
      if(!$attribute->isValid()) {
        continue;
      }

      $schema->addEntityAction($attribute);
    }
  }

  private function fillUniqueProperties(EntitySchemaInterface $schema, SchemaDefinitionsContainer $container): void {
    $uniqueProperties = [];
    foreach ($container->iterateUniquePropertyDefinitions() as $definition) {
      $uniqueProperties[$definition->getName()][] = $definition->getProperty();
    }

    $schema->setUniqueProperties($uniqueProperties);
  }

  private function fillLabelProperties(EntitySchemaInterface $schema, SchemaDefinitionsContainer $container): void {
    $labels = [];
    foreach ($container->iterateLabelDefinitions() as $labelAttribute) {
      $labels[] = [
        'name' => $labelAttribute->getPath(),
        'label' => $labelAttribute->getLabel(),
        'rank' => $labelAttribute->getRank(),
      ];
    }

    $labelProp = array_keys($this->sortProperties($labels));

    $schema->setEntityLabelProperties($labelProp);
  }

  private function fillEntityOverview(EntitySchemaInterface $schema, SchemaDefinitionsContainer $container): void {
    $entityOverview = [];
    foreach ($container->iterateEntityOverviewDefinitions() as $definition) {
      $entityOverview[] = [
        'name' => $definition->getPath(),
        'label' => $definition->getLabel(),
        'rank' => $definition->getRank(),
      ];
    }

    $entityOverview = $this->sortProperties($entityOverview);

    $schema->setEntityOverviewProperties($entityOverview);
  }

  private function fillExtendedEntityOverview(EntitySchemaInterface $schema, SchemaDefinitionsContainer $container): void {
    $extendedEntityOverview = [];
    foreach ($container->iterateExtendedEntityOverviewDefinitions() as $definition) {
      $extendedEntityOverview[] = [
        'name' => $definition->getPath(),
        'label' => $definition->getLabel(),
        'rank' => $definition->getRank(),
      ];
    }

    $extendedEntityOverview = $this->sortProperties($extendedEntityOverview);

    $schema->setExtendedEntityOverviewProperties($extendedEntityOverview);
  }

  private function fillSearchProperties(EntitySchemaInterface $schema, SchemaDefinitionsContainer $container): void {
    $searchProperties = [];
    foreach ($container->iterateSearchPropertyDefinitions() as $definition) {
      $searchProperties[] = $definition->getProperty();
    }

    $schema->setSearchProperties($searchProperties);
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

  private function fillSchemaBasics(EntitySchemaInterface $schema, SchemaDefinitionsContainer $container): bool {
    $attr = $container->getEntityTypeAttr();

    if(!$attr instanceof EntityTypeAttr) {
      return false;
    }

    $schema
      ->setEntityType($attr->getName())
      ->setEntityLabel($attr->getLabel())
    ;

    return true;
  }

  private function fillDatabaseDetails(EntitySchemaInterface $schema, SchemaDefinitionsContainer $container): void {
    foreach ($container->iterateTableReferenceAttributes() as $attribute) {
      if(!$attribute instanceof TableReferenceDefinitionInterface) {
        continue;
      }
      $attribute
        ->setExternalName($schema->getEntityType() . '_' . $attribute->getName())
        ->setFromEntityClass($schema->getEntityClass());

      $schema->addTableReference($attribute);
    }
  }

  private function fillProperties(EntitySchemaInterface $schema, SchemaDefinitionsContainer $schemaContainer): void {
    foreach ($schemaContainer->iteratePropertyContainer() as $container) {
      if(!$container->isValid()) {
        continue;
      }
      $propertyConfiguration = $this->propertyConfigurationBuilder->buildBasicPropertyConfiguration($container, $schema);
      $container->setPropertyConfiguration($propertyConfiguration);
      $schema->addProperty($propertyConfiguration);
    }

    foreach ($schemaContainer->iteratePropertyContainer() as $container) {
      if(!$container->isValid() || !$container->hasPropertyConfiguration()) {
        continue;
      }
      $this->propertyConfigurationBuilder->fillPropertyConfiguration($container, $schema);
    }
  }

  private function buildFilters(EntitySchemaInterface $schema, SchemaDefinitionsContainer $container): void {
    foreach ($container->iterateSqlFilterDefinitionAttributes() as $attribute) {
      if(!$attribute->isValid()) {
        continue;
      }

      $schema->addFilter($attribute);
    }
  }

  private function buildAggregations(EntitySchema $schema, $yaml): void {
    foreach($yaml[self::YAML_PARAM_AGGREGATIONS] as $key => $aggregationArray) {
      $aggregationConfiguration = $this->aggregationConfigurationBuilder->buildAggregationConfiguration($aggregationArray, $key);
      $schema->addAggregation($aggregationConfiguration);
    }
  }

  private function fillDatabase(EntitySchemaInterface $schema, SchemaDefinitionsContainer $container): void {
    $attr = $container->getDatabaseDefinition();

    if(!$attr instanceof DatabaseDefinition || !$attr->isValid()) {
      return;
    }

    $schema->setDatabase($attr->getDatabaseClass());
    $schema->setBaseTable($attr->getBaseTable());
  }

  private function fillEntityServices(EntitySchema $schema, SchemaDefinitionsContainer $container): void {
    foreach ($container->iterateRepositoryDefinitions() as $repositoryDefinition) {
      if(!$repositoryDefinition instanceof RepositoryDefinitionInterface || !$repositoryDefinition->isValid()) {
        continue;
      }
      $list = $this->getVersionList($repositoryDefinition);
      $repositoryDefinition->setVersionList($list);
      $schema->addRepositoryDefinition($repositoryDefinition);
    }

    foreach ($container->iterateBaseQueryDefinitions() as $baseQueryDefinition) {
      if(!$baseQueryDefinition instanceof BaseQueryDefinitionInterface || !$baseQueryDefinition->isValid()) {
        continue;
      }
      $list = $this->getVersionList($baseQueryDefinition);
      $baseQueryDefinition->setVersionList($list);
      $schema->addBaseQueryDefinition($baseQueryDefinition);
    }

    foreach ($container->iterateSimpleSearchDefinitions() as $simpleSearchDefinition) {
      if(!$simpleSearchDefinition instanceof SimpleSearchDefinitionInterface || !$simpleSearchDefinition->isValid()) {
        continue;
      }
      $list = $this->getVersionList($simpleSearchDefinition);
      $simpleSearchDefinition->setVersionList($list);
      $schema->addSimpleSearchDefinition($simpleSearchDefinition);
    }

    foreach ($container->iterateDataProviderDefinitions() as $dataProviderDefinition) {
      if(!$dataProviderDefinition instanceof DataProviderDefinitionInterface || !$dataProviderDefinition->isValid()) {
        continue;
      }
      $list = $this->getVersionList($dataProviderDefinition);
      $dataProviderDefinition->setVersionList($list);
      $schema->addDataProviderDefinition($dataProviderDefinition);
    }

    foreach ($container->iterateCreatorDefinitions() as $creatorDefinition) {
      if(!$creatorDefinition instanceof CreatorDefinitionInterface || !$creatorDefinition->isValid()) {
        continue;
      }
      $list = $this->getVersionList($creatorDefinition);
      $creatorDefinition->setVersionList($list);
      $schema->addCreatorDefinition($creatorDefinition);
    }

    foreach ($container->iterateRefinerDefinitions() as $referenceDefinition) {
      if(!$referenceDefinition instanceof RefinerDefinitionInterface || !$referenceDefinition->isValid()) {
        continue;
      }
      $list = $this->getVersionList($referenceDefinition);
      $referenceDefinition->setVersionList($list);
      $schema->addRefinerDefinition($referenceDefinition);
    }

    foreach ($container->iterateColumnBuilderDefinitions() as $columnBuilderDefinition) {
      if(!$columnBuilderDefinition instanceof ColumnBuilderDefinitionInterface || !$columnBuilderDefinition->isValid()) {
        continue;
      }
      $list = $this->getVersionList($columnBuilderDefinition);
      $columnBuilderDefinition->setVersionList($list);
      $schema->addColumnsBuilderDefinition($columnBuilderDefinition);
    }

    foreach ($container->iterateListProviderDefinitions() as $listProviderDefinition) {
      if(!$listProviderDefinition instanceof ListProviderDefinitionInterface || !$listProviderDefinition->isValid()) {
        continue;
      }
      $list = $this->getVersionList($listProviderDefinition);
      $listProviderDefinition->setVersionList($list);
      $schema->addListProviderDefinition($listProviderDefinition);
    }

    foreach ($container->iterateAdditionalDataProviderDefinitions() as $additionalDataProviderDefinition) {
      if(!$additionalDataProviderDefinition instanceof AdditionalDataProviderDefinitionInterface || !$additionalDataProviderDefinition->isValid()) {
        continue;
      }
      $list = $this->getVersionList($additionalDataProviderDefinition);
      $additionalDataProviderDefinition->setVersionList($list);
      $schema->addAdditionalDataProviderDefinition($additionalDataProviderDefinition);
    }

    foreach ($container->iterateViewBuilderDefinitions() as $viewBuilderDefinition) {
      if(!$viewBuilderDefinition instanceof ViewBuilderDefinitionInterface || !$viewBuilderDefinition->isValid()) {
        continue;
      }
      $list = $this->getVersionList($viewBuilderDefinition);
      $viewBuilderDefinition->setVersionList($list);
      $schema->addViewBuilderDefinition($viewBuilderDefinition);
    }

    foreach ($container->iterateOverviewBuilderDefinitions() as $overviewBuilderDefinition) {
      if(!$overviewBuilderDefinition instanceof OverviewBuilderDefinitionInterface || !$overviewBuilderDefinition->isValid()) {
        continue;
      }
      $list = $this->getVersionList($overviewBuilderDefinition);
      $overviewBuilderDefinition->setVersionList($list);
      $schema->addOverviewBuilderDefinition($overviewBuilderDefinition);
    }
  }


  private function getVersionList(VersionInformationWrapperInterface $definition): VersionListInterface {
    return $this->versionService->getVersionList($definition);
  }

}