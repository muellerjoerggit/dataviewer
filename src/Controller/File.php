<?php

namespace App\Controller;

use App\Services\FileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class File extends AbstractController {

	#[Route(path: '/file/download/{fileId}', name: 'app_file_download')]
	public function downloadFile(FileService $fileService, int $fileId): Response {
		$file = $fileService->getFile($fileId);

		if(!$file) {
			return new Response('Datei nicht gefunden');
		}

		$filePath = $file->getPath();

		if(!empty($filePath)) {
			$response = $this->file($filePath, $file->getFilename());
			$response->headers->set('Content-type', $file->getMimetype());

			return $response;
		}

		return new Response('Datei nicht gefunden');
	}



}
