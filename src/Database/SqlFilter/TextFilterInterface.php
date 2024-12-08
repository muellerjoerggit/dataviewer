<?php

namespace App\Database\SqlFilter;

interface TextFilterInterface {

	public const FILTER_TYPE_CONTAINS = 'contains';
	public const FILTER_TYPE_EMPTY_STRING = 'empty_string';
	public const FILTER_TYPE_EQUAL = 'equal';
	public const FILTER_TYPE_ONE_OF_WORDS = 'contain_one_of_words';
	public const FILTER_TYPE_CONTAINS_HTML = 'contains_html';

}
