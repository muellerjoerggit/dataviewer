<?php

namespace App\Item\Property\PreDefinedAttributes;

class PreDefined {

  public static function string(): array {
    return array_merge(
      PreDefinedFilter::commonTextFilter(),
      PreDefinedFilter::nullCheckFilter(),
      PreDefinedPreRenderingItemHandler::commonPreRenderingHandler(),
    );
  }

  public static function integer(): array {
    return array_merge(
      PreDefinedFilter::nullCheckFilter(),
      PreDefinedPreRenderingItemHandler::commonPreRenderingHandler(),
    );
  }

  public static function simpleInteger(): array {
    return array_merge(
      PreDefinedPreRenderingItemHandler::commonPreRenderingHandler(),
    );
  }

  public static function dateTime(): array {
    return array_merge(
      PreDefinedFilter::dateTimeFilter(),
      PreDefinedFilter::nullCheckFilter(),
      PreDefinedPreRenderingItemHandler::commonPreRenderingHandler(),
      PreDefinedFormatterItemHandler::dateTimeFormatterHandler(),
    );
  }

  public static function table(): array {
    return array_merge(
      PreDefinedPreRenderingItemHandler::tablePreRenderingHandler(),
    );
  }

}