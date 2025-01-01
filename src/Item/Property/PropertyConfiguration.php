<?php

namespace App\Item\Property;

use App\Database\TableReference\TableReferenceConfiguration;
use App\Item\ItemConfiguration;
use App\Item\ItemInterface;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\ParameterType;

class PropertyConfiguration extends ItemConfiguration {

  public const string YAML_PARAM_COLUMN = 'column';
  public const string YAML_PARAM_FILTER = 'generatedFilter';
  public const string YAML_PARAM_HANDLER = 'handler';
  public const string YAML_PARAM_TABLE_REFERENCE = 'tableReference';
  public const string YAML_PARAM_REFERENCED_COLUMN = 'referencedColumn';

  private string $column;
  private TableReferenceConfiguration $tableReference;

  public function getColumn(): string {
    return $this->column ?? '';
  }

  public function setColumn(string $column): PropertyConfiguration {
    $this->column = $column;
    return $this;
  }

  public function hasColumn(): bool {
    return isset($this->column);
  }

  public function getQueryParameterType(bool $forceArray = false): int {
    $multiple = $forceArray || $this->isCardinalityMultiple();
    switch ($this->getDataType()) {
      case ItemInterface::DATA_TYPE_BOOL:
        if ($multiple) {
          return ArrayParameterType::INTEGER;
        } else {
          return ParameterType::BOOLEAN;
        }
      case ItemInterface::DATA_TYPE_INTEGER:
        if ($multiple) {
          return ArrayParameterType::INTEGER;
        } else {
          return ParameterType::INTEGER;
        }
      case ItemInterface::DATA_TYPE_FLOAT:
      default:
        if ($multiple) {
          return ArrayParameterType::STRING;
        } else {
          return ParameterType::STRING;
        }
    }
  }

  public function getTableReference(): TableReferenceConfiguration {
    return $this->tableReference;
  }

  public function setTableReference(TableReferenceConfiguration $tableReference): PropertyConfiguration {
    $this->tableReference = $tableReference;
    return $this;
  }

  public function hasTableReference(): bool {
    return isset($this->tableReference);
  }
}