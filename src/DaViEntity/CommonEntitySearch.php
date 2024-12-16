<?php

namespace App\DaViEntity;

class CommonEntitySearch extends AbstractEntitySearch {

  public function __construct(
    CommonEntityDataMapper $dataMapper
  ) {
    parent::__construct($dataMapper);
  }

}