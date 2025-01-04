<?php

namespace App\Database;

use App\DaViEntity\Schema\EntitySchema;
use App\Services\AbstractLocator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class DatabaseLocator extends AbstractLocator {

  public function __construct(
    #[AutowireLocator('services.database.database_interface')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getDatabaseBySchema(EntitySchema $schema): DatabaseInterface {
    $db = $schema->getDatabase();
    return $this->get($db);
  }

}