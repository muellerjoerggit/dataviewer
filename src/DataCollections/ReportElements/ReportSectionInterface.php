<?php

namespace App\DataCollections\ReportElements;

use Generator;

interface ReportSectionInterface {

  public function toArray(): array;

  public function getId(): int;

  public function addChild(ReportElementInterface $child): ReportSectionInterface;

  /**
   * @return Generator<ReportElementInterface>
   */
  public function iterateChildren(): Generator;

}