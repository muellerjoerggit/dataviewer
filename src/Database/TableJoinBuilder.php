<?php

namespace App\Database;

use App\Database\Exceptions\NotJoinableException;
use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\Item\ItemHandler_EntityReference\EntityReferenceItemHandlerLocator;
use App\Item\ItemHandler_EntityReference\SimpleEntityReferenceJoinInterface;

class TableJoinBuilder {

  public function __construct(
    private readonly EntityTypeSchemaRegister $schemaRegister,
    private readonly EntityReferenceItemHandlerLocator $referenceItemHandlerLocator,
  ) {}

  /**
   * @throws NotJoinableException
   */
  public function joinFromPropertyPath(DaViQueryBuilder $queryBuilder, EntitySchema $schema, string $path): void {
    $pathParts = explode('.', $path);
    $currentSchema = $schema;

    foreach ($pathParts as $property) {
      $propertyConfig = $currentSchema->getProperty($property);
      if($propertyConfig->hasEntityReferenceHandler()) {
        $handler = $this->referenceItemHandlerLocator->getEntityReferenceHandlerFromItem($propertyConfig);

        if(!$handler instanceof SimpleEntityReferenceJoinInterface) {
          $handlerClass = get_class($handler);
          throw new NotJoinableException("Handler $handlerClass does not implement SimpleEntityReferenceJoinInterface");
        }

        $handler->joinTable($queryBuilder, $propertyConfig, $schema);
        [$targetEntityClass, $property] = $handler->getTargetSetting($propertyConfig);
      } else {
        break;
      }

      $currentSchema = $this->schemaRegister->getSchemaFromEntityClass($targetEntityClass);
    }
  }

}