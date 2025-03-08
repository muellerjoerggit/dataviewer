<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

<?= $useStatements; ?>

class <?= $class_name; ?> extends AbstractRepository {

  public function __construct(
    EntityTypesRegister $entityTypesRegister,
    MainRepository $mainRepository,
    DataProviderLocator $entityDataProviderLocator,
    CreatorLocator $entityCreatorLocator,
    AdditionalDataProviderLocator $additionalDataProviderLocator,
    RefinerLocator $entityRefinerLocator,
    ListProviderLocator $entityListProviderLocator,
    ValidatorLocator $validatorLocator,
  ) {
    parent::__construct(
      $entityTypesRegister,
      $mainRepository,
      $entityDataProviderLocator,
      $entityCreatorLocator,
      $additionalDataProviderLocator,
      $entityRefinerLocator,
      $entityListProviderLocator,
      $validatorLocator,
      <?= $entityClass; ?>
    );
  }

}
