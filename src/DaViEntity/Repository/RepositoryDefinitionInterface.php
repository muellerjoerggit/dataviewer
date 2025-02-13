<?php

namespace App\DaViEntity\Repository;

interface RepositoryDefinitionInterface {

  public function getRepositoryClass(): string;

  public function isValid(): bool;

}