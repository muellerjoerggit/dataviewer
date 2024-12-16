<?php

namespace App\Item;

use App\DaViEntity\EntityKey;
use Generator;

interface ReferenceItemInterface {

  public function iterateEntityKeys(): Generator;

  public function getEntityKey(): array|EntityKey;

  public function countEntityKeys(): int;

  public function addEntityKey(EntityKey|array $entityKeys): ItemInterface;

  public function hasEntityKeys(): bool;

  public function getFirstEntityKey(): EntityKey;

}