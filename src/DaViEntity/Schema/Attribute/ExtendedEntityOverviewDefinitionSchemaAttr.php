<?php

namespace App\DaViEntity\Schema\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class ExtendedEntityOverviewDefinitionSchemaAttr extends AbstractOverviewDefinition implements ExtEntityOverviewDefinitionInterface {

}