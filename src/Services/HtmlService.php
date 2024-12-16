<?php

namespace App\Services;

use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;

class HtmlService {

  public function __construct(
    private readonly HtmlSanitizerInterface $htmlSanitizer
  ) {}

  public function sanitizeHtml(string $html): string {
    return $this->htmlSanitizer->sanitize($html);
  }

  public function htmlToText(string $html): string {
    $html = $this->htmlSanitizer->sanitizeFor('style', $html);
    $html = $this->htmlSanitizer->sanitizeFor('script', $html);
    $html = str_replace(['<br>', '<br />', '<br/>'], ' ', $html);

    foreach (['p', 'div', 'li'] as $tag) {
      $tag = '</' . $tag . '>';
      $html = str_replace($tag, $tag . ' ', $html);
    }

    return $this->stripTags($html);
  }

  public function stripTags(string $html): string {
    return trim(strip_tags(html_entity_decode($html)));
  }

}
