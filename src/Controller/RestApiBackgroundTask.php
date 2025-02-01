<?php

namespace App\Controller;

use App\Services\BackgroundTask\BackgroundTaskService;
use App\SymfonyEntity\BackgroundTask;
use App\SymfonyEntity\TaskResult;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RestApiBackgroundTask extends AbstractController {

	#[Route(path: '/api/task/get/{taskId}', name: 'app_api_task_get')]
	public function apiGetTask(BackgroundTaskService $backgroundTaskService, int $taskId): Response {
		return $this->json($backgroundTaskService->getBackgroundTaskData($taskId));
	}

	#[Route(path: '/api/task/terminate/{taskId}', name: 'app_api_terminate_task')]
	public function terminateTask(BackgroundTaskService $backgroundTaskService, int $taskId): void {
		$backgroundTaskService->terminateTask($taskId);
	}

	#[Route(path: '/api/task/result/get/{taskId}', name: 'app_api_task_result')]
	public function getTaskResult(BackgroundTaskService $backgroundTaskService, int $taskId): Response {
		$task = $backgroundTaskService->getBackgroundTaskByTaskId($taskId);
		if(!($task instanceof BackgroundTask) || $task->getStatus() !== BackgroundTask::STATUS_FINISHED) {
			return $this->json([]);
		}

		$data = $this->buildResultData($task);

		$ret = [
			'result' => !empty($data),
			'data' => $data
		];

		return $this->json($ret);
	}

	private function buildResultData(BackgroundTask $task): array {
		$taskResult = $task->getTaskResults();

    $ret = [];
    try {
      foreach ($taskResult->getIterator() as $result) {
        $type = $result->getType();
        if(!in_array($type, TaskResult::VALID_TYPES)) {
          return [];
        }

        $data['type'] = $type;
        $result = json_decode($result->getResult(), true);

        switch ($type) {
          case TaskResult::TYPE_FILE:
            $fileId = $result['fileId'] ?? 0;
            $data['type'] = TaskResult::TYPE_URL;
            $data['urls'] = [[
              'label' => 'Datei',
              'url' => $this->generateUrl('app_file_download', ['fileId' => $fileId], UrlGeneratorInterface::ABSOLUTE_URL)
            ]];
            break;
          case TaskResult::TYPE_ENTITY_LIST:
            $data['entityList'] = $result['entityList'] ?? [];
        }
        $ret[] = $data;
      }
    } catch (Exception $e) {

    }

		return $ret;
	}

}
