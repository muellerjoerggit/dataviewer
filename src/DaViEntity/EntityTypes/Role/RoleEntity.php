<?php

namespace App\DaViEntity\EntityTypes\Role;

use App\DaViEntity\AbstractEntity;
use App\DaViEntity\Attribute\EntityType;
use App\DaViEntity\Traits\EntityPropertyTrait;

#[EntityType(name: "Role")]
class RoleEntity extends AbstractEntity {

  use EntityPropertyTrait;

  private $rol_id;
  private $title;
  private $description;

}