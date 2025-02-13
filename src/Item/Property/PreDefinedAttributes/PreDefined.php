<?php

namespace App\Item\Property\PreDefinedAttributes;

class PreDefined {

  public static function string(): array {
    return array_merge(
      PreDefinedFilter::commonTextFilter(),
      PreDefinedFilter::nullCheckFilter(),
      PreDefinedItemHandler::commonPreRenderingHandler(),
    );
  }

  public static function integer(): array {
    return array_merge(
      PreDefinedFilter::nullCheckFilter(),
      PreDefinedItemHandler::commonPreRenderingHandler(),
    );
  }

  public static function dateTime(): array {
    return array_merge(
      PreDefinedFilter::dateTimeFilter(),
      PreDefinedFilter::nullCheckFilter(),
      PreDefinedItemHandler::commonPreRenderingHandler(),
    );
  }

  public static function table(): array {
    return array_merge(
      PreDefinedItemHandler::tablePreRenderingHandler(),
    );
  }

}