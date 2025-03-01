<?php

namespace App\Services\ProgressTracker;

interface TrackerInterface {

  public function setProgress(mixed $progress): void;

  public function isTerminated(): bool;

  public function shouldProgressBeRecorded(int $count): bool;

}