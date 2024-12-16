<?php

namespace App\Item\ItemHandler_PreRendering;

use App\Item\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('pre_rendering_item_handler')]
interface PreRenderingItemHandlerInterface {

  public function getExtendedOverview(ItemInterface $item, array $options): array;

}
