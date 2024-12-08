<?php

namespace App\Item\ItemHandler_PreRendering;

use App\Item\ItemHandler_EntityReference\EntityReferenceItemHandlerLocator;
use App\Item\ItemHandler_ValueFormatter\ValueFormatterItemHandlerLocator;
use App\DaViEntity\EntityInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EntityReferenceOverviewPreRenderingItemHandler extends EntityReferencePreRenderingItemHandler  {

	public function __construct(EntityReferenceItemHandlerLocator $referenceHandlerLocator, ValueFormatterItemHandlerLocator $formatterLocator, UrlGeneratorInterface $router) {
		parent::__construct($referenceHandlerLocator, $formatterLocator, $router);
	}

	public function getComponentPreRenderArray(EntityInterface $entity, string $property): array {
		$ret = parent::getComponentPreRenderArray($entity, $property);
		$ret['component'] = 'EntityOverviewItem';

		return $ret;
	}

}
