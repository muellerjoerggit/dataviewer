<?php

namespace App\Services;

use DateTime;
use DateTimeInterface;

class TimeConverter {

	public function secondsToTime(float $seconds): string {
		$seconds = round($seconds);
		if($seconds === 0) {
			return '00:00';
		}
		return sprintf('%02d:%02d:%02d', $seconds/3600, floor($seconds/60)%60, $seconds%60);
	}

  public function randomDateTime(DateTimeInterface $begin, DateTimeInterface $end, string $format = 'Y-m-d H:i:s'): string {
    $begin = $begin->getTimestamp();
    $end = $end->getTimestamp();

    $random = mt_rand($begin, $end);

    $datetime = new DateTime();
    $datetime->setTimestamp($random);
    return $datetime->format($format);
  }

}
