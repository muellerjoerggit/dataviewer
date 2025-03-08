<?php

namespace App\Development\Maker;

use App\Database\BaseQuery\CommonBaseQuery;
use App\Database\DatabaseLocator;
use App\Database\DaViDatabaseOne;
use App\Database\DaViDatabaseTwo;
use App\DaViEntity\AbstractEntity;
use App\DaViEntity\Traits\EntityPropertyTrait;
use App\EntityServices\AggregatedData\SqlAggregatedDataProvider;
use App\EntityServices\ColumnBuilder\CommonColumnBuilder;
use App\EntityServices\Creator\CommonCreator;
use App\EntityServices\DataProvider\CommonSqlDataProvider;
use App\EntityServices\EntityLabel\CommonLabelCrafter;
use App\EntityServices\ListProvider\CommonListProvider;
use App\EntityServices\OverviewBuilder\CommonOverviewBuilder;
use App\EntityServices\Refiner\CommonRefiner;
use App\EntityServices\SimpleSearch\CommonSimpleSearch;
use App\EntityServices\Validator\ValidatorBase;
use App\EntityServices\ViewBuilder\CommonViewBuilder;
use App\Item\DataType;
use App\Item\ItemConfigurationInterface;
use App\Item\Property\Attribute\DatabaseColumnDefinition;
use App\Item\Property\Attribute\EntityOverviewPropertyDefinition;
use App\Item\Property\Attribute\PropertyPreDefinedDefinition;
use App\Item\Property\Attribute\SearchPropertyDefinition;
use App\Services\AppNamespaces;
use App\Services\ClientService;
use App\Services\DirectoryFileService;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\BigIntType;
use Doctrine\DBAL\Types\BooleanType;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\DateType;
use Doctrine\DBAL\Types\DecimalType;
use Doctrine\DBAL\Types\FloatType;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\TextType;
use Exception;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;
use App\DaViEntity\Schema\Attribute\EntityTypeDefinition;
use App\EntityServices\Repository\RepositoryDefinition;
use App\Database\BaseQuery\BaseQueryDefinition;
use App\EntityServices\ColumnBuilder\ColumnBuilderDefinition;
use App\EntityServices\Refiner\RefinerDefinition;
use App\EntityServices\Creator\CreatorDefinition;
use App\EntityServices\SimpleSearch\SimpleSearchDefinition;
use App\EntityServices\DataProvider\DataProviderDefinition;
use App\EntityServices\ListProvider\ListProviderDefinition;
use App\EntityServices\OverviewBuilder\OverviewBuilderDefinition;
use App\EntityServices\ViewBuilder\ViewBuilderDefinition;
use App\EntityServices\AggregatedData\SqlAggregatedDataProviderDefinition;
use App\EntityServices\EntityLabel\LabelCrafterDefinition;
use App\EntityServices\Validator\ValidatorDefinition;
use App\DaViEntity\Schema\Attribute\DatabaseDefinition;
use App\Item\Property\Attribute\LabelPropertyDefinition;
use App\Item\Property\Attribute\PropertyDefinition;
use App\Item\Property\Attribute\UniquePropertyDefinition;
use App\Item\Property\PreDefinedAttributes\PreDefined;
use App\Item\Property\PropertyItemInterface;
use App\DaViEntity\MainRepository;
use App\DaViEntity\Schema\EntityTypesRegister;
use App\EntityServices\AdditionalData\AdditionalDataProviderLocator;
use App\EntityServices\Creator\CreatorLocator;
use App\EntityServices\DataProvider\DataProviderLocator;
use App\EntityServices\ListProvider\ListProviderLocator;
use App\EntityServices\Refiner\RefinerLocator;
use App\EntityServices\Repository\AbstractRepository;
use App\EntityServices\Validator\ValidatorLocator;

/**
 * @method string getCommandDescription()
 */
class MakeEntityType extends AbstractMaker {

  public function __construct(
    private readonly DatabaseLocator $databaseLocator,
    private readonly DirectoryFileService $directoryFileService,
    private readonly ClientService $clientService
  ) {}

  public static function getCommandName(): string {
    return 'make:davi-entity';
  }

  public static function getCommandDescription(): string {
    return 'Create a new DaVi entity type';
  }

  public function configureCommand(Command $command, InputConfiguration $inputConfig) {
    $command
      ->addArgument('entity-type', InputArgument::REQUIRED, 'Choose a name of your entity type class')
      ->addArgument('entity-type-label', InputArgument::REQUIRED, 'Entity label')
      ->addArgument('basetable', InputArgument::REQUIRED, 'Input the table name')
      ->addArgument('database', InputArgument::REQUIRED, 'Database of the table: 1) one 2) two')
      ->addArgument('abbreviation', InputArgument::REQUIRED, 'Error code abbreviation');
  }

  public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator) {
    $fileSystem = new Filesystem();
    $entityType = $input->getArgument('entity-type');
    $entityTypeLabel = $input->getArgument('entity-type-label');
    $baseTable = $input->getArgument('basetable');
    $abbreviation = strtoupper($input->getArgument('abbreviation'));
    $client = $this->clientService->getFirstClientId();
    if (in_array($input->getArgument('database'), ["1", "2"])) {
      $database = $input->getArgument('database') == 1 ? DaViDatabaseOne::class : DaViDatabaseTwo::class;
      $schemaManager = $this->databaseLocator->getDatabase($database)->createSchemaManager($client);
    } else {
      $io->text('Database not found');
      return;
    }

    $repositoryClassNameDetails = $generator->createClassNameDetails(
      $entityType,
      'EntityTypes\\' . $entityType,
      'Repository'
    );

    $useStatementsRepository = new UseStatementGenerator([
      MainRepository::class,
      EntityTypesRegister::class,
      AdditionalDataProviderLocator::class,
      CreatorLocator::class,
      DataProviderLocator::class,
      ListProviderLocator::class,
      RefinerLocator::class,
      AbstractRepository::class,
      ValidatorLocator::class,
    ]);

    $entityClassNameDetails = $generator->createClassNameDetails(
      $entityType,
      'EntityTypes\\' . $entityType,
      'Entity'
    );

    $useStatementsEntity = new UseStatementGenerator([
      AbstractEntity::class,
      PropertyItemInterface::class,
      DataType::class,
      EntityTypeDefinition::class,
      RepositoryDefinition::class,
      BaseQueryDefinition::class,
      ColumnBuilderDefinition::class,
      RefinerDefinition::class,
      CreatorDefinition::class,
      SimpleSearchDefinition::class,
      DataProviderDefinition::class,
      ListProviderDefinition::class,
      OverviewBuilderDefinition::class,
      ViewBuilderDefinition::class,
      SqlAggregatedDataProviderDefinition::class,
      LabelCrafterDefinition::class,
      ValidatorDefinition::class,
      DatabaseDefinition::class,
      LabelPropertyDefinition::class,
      PropertyDefinition::class,
      UniquePropertyDefinition::class,
      PreDefined::class,
      EntityPropertyTrait::class,
      CommonBaseQuery::class,
      CommonColumnBuilder::class,
      CommonRefiner::class,
      CommonCreator::class,
      CommonSimpleSearch::class,
      CommonSqlDataProvider::class,
      CommonListProvider::class,
      CommonOverviewBuilder::class,
      CommonViewBuilder::class,
      SqlAggregatedDataProvider::class,
      CommonLabelCrafter::class,
      ValidatorBase::class,
      DatabaseColumnDefinition::class,
      PropertyPreDefinedDefinition::class,
      SearchPropertyDefinition::class,
      EntityOverviewPropertyDefinition::class,
      $database,
    ]);

    try {
      $columns = $schemaManager->listTableColumns($baseTable);
      $indexes = $schemaManager->listTableIndexes($baseTable);
    } catch (Exception $exception) {
      $io->text('Schemamanager Fehler');
      return;
    }

    $properties = [];
    $primaryColumn = '';

    if (empty($columns)) {
      $io->text('No columns');
      return;
    }

    $primaryColumns = [];
    foreach ($indexes as $index) {
      if ($index->isPrimary()) {
        $primaryColumns = $index->getColumns();
        break;
      }
    }

    foreach ($columns as $column) {
      $type = $this->mapItemTypeFromColumn($column);

      if (empty($type)) {
        continue;
      }

      $isPrimary = in_array($column->getName(), $primaryColumns);
      $isLabel = $this->isLabelColumn($column);

      $properties[$column->getName()] = [
        'column' => $column->getName(),
        'cardinality' => ItemConfigurationInterface::CARDINALITY_SINGLE,
        'isLabel' => $isLabel,
        'isOverview' => $isPrimary || $isLabel,
        'isPrimary' => $isPrimary,
        'type' => $type,
        'preDefined' => $this->mapPredefinedFromColumn($column),
        'null' => $column->getNotnull(),
        'length' => $column->getLength(),
      ];
    }

    $skeletonDir = $this->directoryFileService->getSrcDir() . '/Development/Maker/Skeleton/';
    $repositoryTemplatePath = $skeletonDir . 'Repository.tpl.php';
    $entityTemplatePath = $skeletonDir . 'Entity.tpl.php';

    if (!$repositoryTemplatePath || !$entityTemplatePath) {
      $io->text('Skeleton Templates don´t exists');
      return;
    }

    $generator->generateController(
      $repositoryClassNameDetails->getFullName(),
      $repositoryTemplatePath,
      [
        'useStatements' => $useStatementsRepository,
        'entityClass' => $this->getClassString($entityClassNameDetails->getFullName()),
      ]
    );

    $generator->generateController(
      $entityClassNameDetails->getFullName(),
      $entityTemplatePath,
      [
        'entityType' => $entityType,
        'entityTypeLabel' => $entityTypeLabel,
        'baseTable' => $baseTable,
        'useStatements' => $useStatementsEntity,
        'properties' => $properties,
        'database' => $this->getClassString($database),
        'unique_value' => $primaryColumn,
        'unique_value_exists' => !empty($primaryColumn),
        'repositoryClass' => $this->getClassString($repositoryClassNameDetails->getFullName()),
      ]
    );

    $yaml = [
      'abbreviation' => $abbreviation,
      'codes' => [],
    ];

    $yaml = Yaml::dump($yaml, 2, 4, 1);

    $errorCodesPath = $this->directoryFileService->getEntityTypesDir() . '/' . $entityType . '/' . $entityType . 'ErrorCodes.yaml';
    $fileSystem->dumpFile($errorCodesPath, $yaml);

    $generator->writeChanges();

    $this->writeSuccessMessage($io);
    $io->text('Entity Type created successfully.');
  }

  private function mapItemTypeFromColumn(Column $column): string {
    $type = $column->getType();

    if ($type instanceof StringType || $type instanceof TextType) {
      return 'DataType::STRING';
    } elseif ($type instanceof IntegerType || $type instanceof BigIntType) {
      return 'DataType::INTEGER';
    } elseif ($type instanceof DateTimeType) {
      return 'DataType::DATE_TIME';
    } elseif ($type instanceof DateType) {
      return 'DataType::DATE';
    } elseif ($type instanceof BooleanType) {
      return 'DataType::BOOL';
    } elseif ($type instanceof FloatType || $type instanceof DecimalType) {
      return 'DataType::FLOAT';
    }

    return '';
  }

  private function mapPredefinedFromColumn(Column $column): string {
    $type = $column->getType();

    if ($type instanceof StringType || $type instanceof TextType) {
      return 'PreDefined::class, \'string\'';
    } elseif ($type instanceof IntegerType || $type instanceof BigIntType) {
      return 'PreDefined::class, \'integer\'';
    } elseif ($type instanceof DateTimeType) {
      return 'PreDefined::class, \'dateTime\'';
    }

    return '';
  }

  private function getClassString(string $class): string {
    return AppNamespaces::getShortName($class) . '::class';
  }

  private function isLabelColumn(Column $column): bool {
    $ret = false;

    if(preg_match(
      "(titel|title|name|bez)",
      $column->getName())
    ) {
      $ret = true;
    }

    return $ret;
  }

  public function configureDependencies(DependencyBuilder $dependencies) {}

}
