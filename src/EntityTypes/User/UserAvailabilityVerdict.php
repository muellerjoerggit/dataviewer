<?php

namespace App\EntityTypes\User;

use App\DaViEntity\EntityInterface;
use App\EntityServices\AvailabilityVerdict\AbstractAvailabilityVerdict;
use App\Logger\LogItems\LogItem;
use App\Logger\LogLevels;

class UserAvailabilityVerdict extends AbstractAvailabilityVerdict {

  public function setAvailability(EntityInterface $entity): void {
    $item = $entity->getPropertyItem('active');
    if($item->getCastValues() === false) {
      $entity->setAvailability(false);
      $this->setItemError($item, LogLevels::WARNING);
      $entity->addLogs(LogItem::createAvailabilityLogItem());
    }
  }

}