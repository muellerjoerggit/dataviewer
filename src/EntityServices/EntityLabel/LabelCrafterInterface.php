<?php

namespace App\EntityServices\EntityLabel;

use App\DaViEntity\EntityInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('entity_management.label_crafter')]
interface LabelCrafterInterface {

  public function appendEntityLabelToRows(string | EntityInterface $entityClass, array $rows): array;

  public function getEntityLabel(EntityInterface $entity): string;

}