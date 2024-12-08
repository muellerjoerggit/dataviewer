<?php

namespace App\Logger\LogItems;

interface LogItemInterface {

	public const LOG_LEVEL_DAVI_CRITICAL = 'davi_critical';
	public const LOG_LEVEL_DAVI_ERROR = 'davi_error';
	public const LOG_LEVEL_DAVI_WARNING = 'davi_warning';
	public const LOG_LEVEL_DAVI_NOTICE = 'davi_notice';
	public const LOG_LEVEL_DAVI_DEBUG = 'davi_debug';

	public const LOG_LEVEL_CRITICAL = 'critical';
	public const LOG_LEVEL_ERROR = 'error';
	public const LOG_LEVEL_WARNING = 'warning';
	public const LOG_LEVEL_NOTICE = 'notice';
	public const LOG_LEVEL_INFO = 'info';

	public const LOG_LEVELS = [
		self::LOG_LEVEL_CRITICAL,
		self::LOG_LEVEL_ERROR,
		self::LOG_LEVEL_WARNING,
		self::LOG_LEVEL_NOTICE,
		self::LOG_LEVEL_INFO
	];

	/**
	 * internal log-levels of DaVi
	 */
	public const INTERNAL_LOG_LEVELS = [
		self::LOG_LEVEL_DAVI_ERROR,
		self::LOG_LEVEL_DAVI_CRITICAL,
		self::LOG_LEVEL_DAVI_WARNING,
		self::LOG_LEVEL_DAVI_NOTICE,
		self::LOG_LEVEL_DAVI_DEBUG
	];

	public const RED_LOG_LEVELS = [
		self::LOG_LEVEL_DAVI_CRITICAL,
		self::LOG_LEVEL_DAVI_ERROR,
		self::LOG_LEVEL_ERROR,
		self::LOG_LEVEL_CRITICAL,
	];

	public const YELLOW_LOG_LEVELS = [
		self::LOG_LEVEL_DAVI_WARNING,
		self::LOG_LEVEL_DAVI_NOTICE,
		self::LOG_LEVEL_WARNING,
		self::LOG_LEVEL_NOTICE,
	];

	public function getPreRenderingHandler(): string;

	public static function getType(): string;

	public function getMessage(): string;
}
