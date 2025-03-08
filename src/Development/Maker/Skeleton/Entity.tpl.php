<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

<?= $useStatements; ?>

#[RepositoryDefinition(repositoryClass: <?= $repositoryClass ?>,)]
#[EntityTypeDefinition(
    name: '<?= $entityType ?>',
    label: '<?= $entityTypeLabel ?>',
  ),
]
#[BaseQueryDefinition(baseQueryClass: CommonBaseQuery::class),
  ColumnBuilderDefinition(columnBuilderClass: CommonColumnBuilder::class),
  RefinerDefinition(refinerClass: CommonRefiner::class),
  CreatorDefinition(creatorClass: CommonCreator::class),
  SimpleSearchDefinition(simpleSearchClass: CommonSimpleSearch::class),
  DataProviderDefinition(dataProviderClass: CommonSqlDataProvider::class),
  ListProviderDefinition(listProviderClass: CommonListProvider::class),
  OverviewBuilderDefinition(overviewBuilderClass: CommonOverviewBuilder::class),
  ViewBuilderDefinition(viewBuilderClass: CommonViewBuilder::class),
  SqlAggregatedDataProviderDefinition(aggregatedDataProviderClass: SqlAggregatedDataProvider::class),
  LabelCrafterDefinition(labelCrafterClass: CommonLabelCrafter::class),
  ValidatorDefinition(validatorClass: ValidatorBase::class),
]
#[DatabaseDefinition(
    databaseClass: <?= $database; ?>,
    baseTable: '<?= $baseTable; ?>',
  ),
]
class <?= $class_name; ?> extends AbstractEntity {

	use EntityPropertyTrait;

<?php foreach ($properties as $property): ?>

  /** ########################################################## <?= $property['column']; ?> */
    #[PropertyDefinition(
        dataType: <?= $property['type']; ?>,
      ),
      DatabaseColumnDefinition,
      <?= $property['isPrimary'] ? "UniquePropertyDefinition,\n" : "\n"; ?>
      <?= $property['isLabel'] ? "LabelPropertyDefinition,\n SearchPropertyDefinition,\n" : "\n"; ?>
      <?= $property['isOverview'] ? "EntityOverviewPropertyDefinition,\n" : "\n"; ?>
    ]
    <?= !empty($property['preDefined']) ? "\n      #[PropertyPreDefinedDefinition([\n      [{$property['preDefined']}],\n    ])]\n" : "\n"; ?>
    private PropertyItemInterface $<?= $property['column']; ?>;
  /** ################### */
<?php endforeach; ?>

}
