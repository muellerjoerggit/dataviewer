<?php

namespace App\Item\ItemHandler;

interface ItemHandlerInterface {

	public const string HANDLER_ENTITY_REFERENCE = 'entityReferenceItemHandler';
	public const string HANDLER_PRE_RENDERING = 'preRenderingItemHandler';
	public const string HANDLER_VALUE_FORMATTER = 'valueFormatterItemHandler';
	public const string HANDLER_ADDITIONAL_DATA = 'additionalDataItemHandler';
	public const string HANDLER_VALIDATOR = 'validatorItemHandler';
//	public const string HANDLER_PARAMETER_LIST = 'parameterListItemHandler';
//	public const string HANDLER_DYNAMIC_CONFIGURATION = 'dynamicConfigurationItemHandler';
//	public const string HANDLER_PARSER = 'parserItemHandler';
//	public const string HANDLER_SQL_EXPRESSION = 'sqlExpressionItemHandler';
//	public const string HANDLER_AVAILABILITY = 'availabilityItemHandler';

}
