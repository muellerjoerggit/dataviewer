<?php

namespace App\Item\Property;

use App\Item\Property\Attribute\EntityOverviewPropertyAttr;
use App\Item\Property\Attribute\ExtendedEntityOverviewPropertyAttr;
use App\Item\Property\Attribute\LabelPropertyAttr;
use App\Item\Property\Attribute\PropertyAttr;
use App\Item\Property\Attribute\SearchPropertyAttr;
use App\Item\Property\Attribute\UniquePropertyAttr;

class PropertyAttributesContainer {

  private PropertyAttr $propertyAttr;

  private LabelPropertyAttr $labelPropertyAttr;

  private EntityOverviewPropertyAttr $entityOverviewPropertyAttr;

  private SearchPropertyAttr $searchPropertyAttr;

  private UniquePropertyAttr $uniquePropertyAttr;

  private ExtendedEntityOverviewPropertyAttr $extendedEntityOverviewPropertyAttr;

  public function getPropertyAttr(): PropertyAttr {
    return $this->propertyAttr;
  }

  public function setPropertyAttr(PropertyAttr $propertyAttr): PropertyAttributesContainer {
    $this->propertyAttr = $propertyAttr;
    return $this;
  }

  public function getLabelPropertyAttr(): LabelPropertyAttr {
    return $this->labelPropertyAttr;
  }

  public function setLabelPropertyAttr(LabelPropertyAttr $labelPropertyAttr): PropertyAttributesContainer {
    $this->labelPropertyAttr = $labelPropertyAttr;
    return $this;
  }

  public function getEntityOverviewPropertyAttr(): EntityOverviewPropertyAttr {
    return $this->entityOverviewPropertyAttr;
  }

  public function setEntityOverviewPropertyAttr(EntityOverviewPropertyAttr $entityOverviewPropertyAttr): PropertyAttributesContainer {
    $this->entityOverviewPropertyAttr = $entityOverviewPropertyAttr;
    return $this;
  }

  public function getSearchPropertyAttr(): SearchPropertyAttr {
    return $this->searchPropertyAttr;
  }

  public function setSearchPropertyAttr(SearchPropertyAttr $searchPropertyAttr): PropertyAttributesContainer {
    $this->searchPropertyAttr = $searchPropertyAttr;
    return $this;
  }

  public function getUniquePropertyAttr(): UniquePropertyAttr {
    return $this->uniquePropertyAttr;
  }

  public function setUniquePropertyAttr(UniquePropertyAttr $uniquePropertyAttr): PropertyAttributesContainer {
    $this->uniquePropertyAttr = $uniquePropertyAttr;
    return $this;
  }

  public function getExtendedEntityOverviewPropertyAttr(): ExtendedEntityOverviewPropertyAttr {
    return $this->extendedEntityOverviewPropertyAttr;
  }

  public function setExtendedEntityOverviewPropertyAttr(ExtendedEntityOverviewPropertyAttr $extendedEntityOverviewPropertyAttr): PropertyAttributesContainer {
    $this->extendedEntityOverviewPropertyAttr = $extendedEntityOverviewPropertyAttr;
    return $this;
  }

}