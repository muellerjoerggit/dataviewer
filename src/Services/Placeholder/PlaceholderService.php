<?php

namespace App\Services\Placeholder;

use App\DaViEntity\EntityInterface;

class PlaceholderService {

  public function prepareInsertPlaceholders(PlaceholderConfigInterface $config, EntityInterface $entity, string $text): string {
    $placeholdersValues = $this->preparePlaceholders($config, $entity);
    return $this->insertPlaceholders($placeholdersValues, $text);
  }

  public function preparePlaceholders(PlaceholderConfigInterface $config, EntityInterface $entity): array {
    $placeholders = $config->getPlaceholderConfig();
    $values = [];
    foreach ($placeholders as $placeholder => $property) {
      $fullPlaceholder = '{' . $placeholder . '}';
      $item = $entity->getPropertyItem($property);
      if ($entity->hasPropertyItem($property)) {
        $value = $item->getFirstValueAsString();
        $values[$fullPlaceholder] = $value;
      } else {
        $values[$fullPlaceholder] = $placeholder;
      }
    }
    return $values;
  }

  public function insertPlaceholders(array $placeholdersValues, string $text): string {
    return strtr($text, $placeholdersValues);
  }

}