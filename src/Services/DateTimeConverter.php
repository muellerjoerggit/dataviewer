<?php

namespace App\Services;

use DateMalformedStringException;
use DateTime;
use DateTimeInterface;

class DateTimeConverter {

  public function secondsToTime(int $seconds): string {
    if ($seconds === 0) {
      return '00:00';
    }
    return sprintf('%02d:%02d:%02d', $seconds / 3600, floor($seconds / 60) % 60, $seconds % 60);
  }

  public function randomDateTime(DateTimeInterface $begin, DateTimeInterface $end, string $format = 'Y-m-d H:i:s'): string {
    $begin = $begin->getTimestamp();
    $end = $end->getTimestamp();

    $random = mt_rand($begin, $end);

    $datetime = new DateTime();
    $datetime->setTimestamp($random);
    return $datetime->format($format);
  }

  public function formatDateTime(DateTimeInterface | string | null $dateTime): string {
    if($dateTime === null) {
      return '';
    }

    if(is_string($dateTime)) {
      try {
        $dateTime = new DateTime($dateTime);
      } catch (DateMalformedStringException $e) {
        return $dateTime;
      }
    }

    return $dateTime->format('d.m.Y H:i');
  }

}
