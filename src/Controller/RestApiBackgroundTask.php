<?php

namespace App\Controller;

use App\Services\BackgroundTask\BackgroundTaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RestApiBackgroundTask extends AbstractController {

	#[Route(path: '/api/task/get/{taskId}', name: 'app_api_task_get')]
	public function apiGetTask(BackgroundTaskService $backgroundTaskService, int $taskId): Response {
		return $this->json($backgroundTaskService->getBackgroundTaskData($taskId));
	}

	#[Route(path: '/api/task/terminate/{taskId}', name: 'app_api_terminate_task')]
	public function terminateTask(BackgroundTaskService $backgroundTaskService, int $taskId): void {
		$backgroundTaskService->terminateTask($taskId);
	}

//	#[Route(path: '/api/task/getTaskResult/{taskId}', name: 'app_api_task_result')]
//	public function getTaskResult(EntityManagerInterface $entityManager, int $taskId): Response {
//		$task = $this->getTask($entityManager, $taskId);
//		if(!($task instanceof BackgroundTask) || $task->getStatus() !== BackgroundTask::STATUS_FINISHED) {
//			return $this->json([]);
//		}
//
//		$data = $this->buildResultData($task);
//
//		$ret = [
//			'result' => empty($data) ? false : true,
//			'data' => $data
//		];
//
//		return $this->json($ret);
//	}
//
//	private function buildResultData(BackgroundTask $task): array {
//		$taskResult = $task->getTaskResult();
//		$type = $taskResult->getType();
//
//		if(!in_array($type, TaskResult::VALID_TYPES)) {
//			return [];
//		}
//
//		$data['type'] = $type;
//		$result = json_decode($taskResult->getResult(), true);
//
//		switch ($type) {
//			case TaskResult::TYPE_URL:
//				$fileId = $result['fileId'] ?? 0;
//				$data['urls'] = [[
//					'label' => 'Datei',
//					'url' => $this->generateUrl('app_file_download', ['fileId' => $fileId])
//				]];
//				break;
//			case TaskResult::TYPE_ENTITY_LIST:
//				$data['entityList'] = $result['entityList'] ?? [];
//		}
//
//		return $data;
//	}
//
//	private function getTask(EntityManagerInterface $entityManager, int $taskId): BackgroundTask | null {
//		return $entityManager->getRepository(BackgroundTask::class)->find($taskId);
//	}

}
