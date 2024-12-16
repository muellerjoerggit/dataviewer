<?php

namespace App\Item\ItemHandler_PreRendering;

use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityViewBuilderInterface;
use App\Item\ItemHandler_ValueFormatter\ValueFormatterItemHandlerLocator;
use App\Item\ItemInterface;
use App\Services\HtmlService;

class HtmlPreRenderingItemHandler extends AbstractPreRenderingItemHandler {

  public function __construct(
    private readonly HtmlService $htmlSanitizer,
    ValueFormatterItemHandlerLocator $formatterLocator
  ) {
    parent::__construct($formatterLocator);
  }

  public function getComponentPreRenderArray(EntityInterface $entity, string $property): array {
    $item = $entity->getPropertyItem($property);
    $html = [];
    foreach ($item->iterateValues() as $value) {
      $html[] = [
        'html_sanitized' => $this->htmlSanitizer->sanitizeHtml($value),
        'html_raw' => $value,
      ];
    }

    return [
      'component' => 'HtmlItem',
      'name' => $item->getConfiguration()->getName(),
      'documentation' => [
        'label' => $item->getConfiguration()->getLabel(),
        'description' => $item->getConfiguration()->getDescription(),
        //				'deprecated' => $item->getConfiguration()->getDeprecated(),
      ],
      'data' => [
        'html' => $html,
        'isNull' => $item->isValuesNull(),
        'criticalError' => $item->isRedError(),
        'warningError' => $item->isYellowError(),
      ],
    ];
  }

  public function getExtendedOverview(ItemInterface $item, array $options): array {
    $firstValue = $item->getFirstValueAsString();
    $sanitizedHtml = $this->htmlSanitizer->sanitizeHtml($firstValue);
    $cleanedText = $this->htmlSanitizer->htmlToText($sanitizedHtml);

    return [
      'type' => EntityViewBuilderInterface::EXT_OVERVIEW_HTML,
      'data' => [
        'rawHtml' => $firstValue,
        'sanitizedHtml' => $sanitizedHtml,
        'text' => $cleanedText,
      ],
    ];
  }

}
