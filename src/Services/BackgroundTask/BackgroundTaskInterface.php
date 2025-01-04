<?php

namespace App\Services\BackgroundTask;

use App\SymfonyEntity\TaskConfiguration;

interface BackgroundTaskInterface {

	public static function buildTaskConfiguration(TaskConfiguration $taskConfiguration, mixed $configuration): bool;

}
