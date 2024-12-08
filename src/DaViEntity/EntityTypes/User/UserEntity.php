<?php

namespace App\DaViEntity\EntityTypes\User;

use App\DaViEntity\AbstractEntity;
use App\DaViEntity\Attribute\EntityType;
use App\DaViEntity\Traits\EntityPropertyTrait;

#[EntityType(name: 'User')]
class UserEntity extends AbstractEntity {

  use EntityPropertyTrait;

  private $usr_id;
  private $firstname;
  private $lastname;
  private $email;
  private $active;
  private $inactivation_date;

}