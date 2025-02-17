<?php

namespace App\DataCollections\ReportElements;


use Generator;

class ReportSection implements ReportSectionInterface {

  protected const string TYPE = 'section';

  public const string SECTION_LEVEL_2 = 'sectionLevel2';
  public const string SECTION_LEVEL_3 = 'sectionLevel3';

  public const array VALID_VARIANTS = [
    self::SECTION_LEVEL_2,
    self::SECTION_LEVEL_3,
  ];

  protected array $children = [];
  protected string $variant;

  public function __construct(
    protected readonly int $id,
    protected string $headline,
    string $variant = self::SECTION_LEVEL_2,
  ) {
    $this->setVariant($variant);
  }

  public function getId(): int {
    return $this->id;
  }

  public function addChild(ReportElementInterface $child): ReportSectionInterface {
    $this->children[] = $child;
    return $this;
  }

  public function iterateChildren(): Generator {
    foreach ($this->children as $child) {
      yield $child;
    }
  }

  protected function setVariant(string $variant): void {
    $this->variant = in_array($variant, self::VALID_VARIANTS) ? $variant : self::SECTION_LEVEL_2;
  }

  public function toArray(): array {
    return [
      'type' => static::TYPE,
      'sectionId' => $this->id,
      'headline' => $this->headline,
      'anker' => static::TYPE . '_' . $this->id,
      'variant' => $this->variant,
    ];
  }

}