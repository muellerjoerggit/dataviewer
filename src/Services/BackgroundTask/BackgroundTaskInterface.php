<?php

namespace App\Services\BackgroundTask;

use App\SymfonyEntity\TaskConfiguration;

interface BackgroundTaskInterface {

	public static function buildTaskConfiguration(TaskConfiguration $taskConfiguration, mixed $configuration): bool;

  public static function getTaskName(mixed $configuration): string;

  public static function getTaskDescription(mixed $configuration): string;

}
