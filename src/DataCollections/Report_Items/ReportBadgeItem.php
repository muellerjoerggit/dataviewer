<?php

namespace App\DataCollections\Report_Items;

class ReportBadgeItem extends AbstractReportItem {

  public const string ITEM_TYPE = 'badge';

  public const string BADGE_VARIANT_DEFAULT = 'default';
  public const string BADGE_VARIANT_SECONDARY = 'secondary';
  public const string BADGE_VARIANT_OUTLINE = 'outline';
  public const string BADGE_VARIANT_DESTRUCTIVE = 'destructive';

  private const array VALID_VARIANTS = [
    self::BADGE_VARIANT_DEFAULT,
    self::BADGE_VARIANT_SECONDARY,
    self::BADGE_VARIANT_OUTLINE,
    self::BADGE_VARIANT_DESTRUCTIVE,
  ];

  private string $variant;

  public function __construct(
    private string $text,
    string $variant = self::BADGE_VARIANT_DEFAULT,
  ) {
    $this->setVariant($variant);
  }

  public static function create(string $text, string $variant): static {
    return new static($text, $variant);
  }

  public static function createDefault(string $text): static {
    return new static($text, self::BADGE_VARIANT_DEFAULT);
  }

  public static function createWarningBadge(string $text): static {
    return new static($text, self::BADGE_VARIANT_DESTRUCTIVE);
  }

  public static function createSecondaryBadge(string $text): static {
    return new static($text, self::BADGE_VARIANT_SECONDARY);
  }

  public static function createOutlineBadge(string $text): static {
    return new static($text, self::BADGE_VARIANT_OUTLINE);
  }

  public function setVariant(string $variant): void {
    $this->variant = in_array($variant, self::VALID_VARIANTS) ? $variant : self::BADGE_VARIANT_DEFAULT;
  }

  public function toArray(): array {
    return array_merge(
      parent::toArray(),
      [
        'variant' => $this->variant,
        'text' => $this->text,
      ]
    );
  }

}