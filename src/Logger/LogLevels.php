<?php

namespace App\Logger;

abstract class LogLevels {

  public const string INTERNAL_CRITICAL = 'internal_critical';
  public const string INTERNAL_ERROR = 'internal_error';
  public const string INTERNAL_WARNING = 'internal_warning';
  public const string INTERNAL_NOTICE = 'internal_notice';
  public const string INTERNAL_DEBUG = 'internal_debug';

  public const string CRITICAL = 'critical';
  public const string ERROR = 'error';
  public const string WARNING = 'warning';
  public const string NOTICE = 'notice';
  public const string INFO = 'info';

  public const array LOG_LEVELS = [
    self::CRITICAL,
    self::ERROR,
    self::WARNING,
    self::NOTICE,
    self::INFO,
  ];

  public const array RED_LOG_LEVELS = [
    self::INTERNAL_CRITICAL,
    self::INTERNAL_ERROR,
    self::ERROR,
    self::CRITICAL,
  ];

  public const array YELLOW_LOG_LEVELS = [
    self::INTERNAL_WARNING,
    self::INTERNAL_NOTICE,
    self::WARNING,
    self::NOTICE,
  ];

}