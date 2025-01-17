<?php

namespace App\Logger\LogItems;

interface LogItemInterface {

  public const string LOG_LEVEL_INTERNAL_CRITICAL = 'internal_critical';
  public const string LOG_LEVEL_INTERNAL_ERROR = 'internal_error';
  public const string LOG_LEVEL_INTERNAL_WARNING = 'internal_warning';
  public const string LOG_LEVEL_INTERNAL_NOTICE = 'internal_notice';
  public const string LOG_LEVEL_INTERNAL_DEBUG = 'internal_debug';

  public const string LOG_LEVEL_CRITICAL = 'critical';
  public const string LOG_LEVEL_ERROR = 'error';
  public const string LOG_LEVEL_WARNING = 'warning';
  public const string LOG_LEVEL_NOTICE = 'notice';
  public const string LOG_LEVEL_INFO = 'info';

  public const array LOG_LEVELS = [
    self::LOG_LEVEL_CRITICAL,
    self::LOG_LEVEL_ERROR,
    self::LOG_LEVEL_WARNING,
    self::LOG_LEVEL_NOTICE,
    self::LOG_LEVEL_INFO,
  ];

  /**
   * internal log-levels of DaVi
   */
  public const array INTERNAL_LOG_LEVELS = [
    self::LOG_LEVEL_INTERNAL_ERROR,
    self::LOG_LEVEL_INTERNAL_CRITICAL,
    self::LOG_LEVEL_INTERNAL_WARNING,
    self::LOG_LEVEL_INTERNAL_NOTICE,
    self::LOG_LEVEL_INTERNAL_DEBUG,
  ];

  public const array RED_LOG_LEVELS = [
    self::LOG_LEVEL_INTERNAL_CRITICAL,
    self::LOG_LEVEL_INTERNAL_ERROR,
    self::LOG_LEVEL_ERROR,
    self::LOG_LEVEL_CRITICAL,
  ];

  public const array YELLOW_LOG_LEVELS = [
    self::LOG_LEVEL_INTERNAL_WARNING,
    self::LOG_LEVEL_INTERNAL_NOTICE,
    self::LOG_LEVEL_WARNING,
    self::LOG_LEVEL_NOTICE,
  ];

  public static function getType(): string;

  public function getPreRenderingHandler(): string;

  public function getMessage(): string;

}
