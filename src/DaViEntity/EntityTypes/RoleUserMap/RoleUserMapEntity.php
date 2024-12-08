<?php

namespace App\DaViEntity\EntityTypes\RoleUserMap;

use App\DaViEntity\AbstractEntity;
use App\DaViEntity\Attribute\EntityType;
use App\DaViEntity\Traits\EntityPropertyTrait;

#[EntityType(name: 'RoleUserMap')]
class RoleUserMapEntity extends AbstractEntity {

  use EntityPropertyTrait;

  private $usr_id;
  private $rol_id;

}