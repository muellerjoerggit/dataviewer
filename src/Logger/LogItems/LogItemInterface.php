<?php

namespace App\Logger\LogItems;

interface LogItemInterface {

  public static function getType(): string;

  public function getPreRenderingHandler(): string;

  public function getMessage(): string;

}
