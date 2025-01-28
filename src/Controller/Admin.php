<?php

namespace App\Controller;

use App\Services\DirectoryFileRegister;
use App\SymfonyRepository\FileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin')]
class Admin extends AbstractController {

	#[Route(path: '/', name: 'app_admin')]
	public function admin(): Response {
		$urls = [];
		$routes = [
			'app_admin_show_log_dir' => [
				'label' => 'Log',
				'description' => 'Auf Logdateien zugreifen'
			],
			'app_admin_php_info' => [
				'label' => 'PHP Info',
				'description' => 'PHP Info'
			],
		];
		foreach ($routes as $route => $routeData) {
			$urls[] = [
				'label' => $routeData['label'] ?? $route,
				'url' => $this->generateUrl($route),
				'description' => $routeData['description'] ?? ''
			];
		}

		return $this->render('home/homepage.html.twig', [
			'title' => 'Admin',
			'urls' => $urls
		]);
	}

	#[Route(path: '/show-log-dir', name: 'app_admin_show_log_dir')]
	public function showLogDir(DirectoryFileRegister $directoryFileRegister): Response {
		$logDir = $directoryFileRegister->getLogDir();
		$ret = [
			'files' => [],
			'error' => '',
			'logDir' => $logDir
		];
		$finderResult = [];
		if(!empty($logDir)) {
			try {
				$finder = new Finder();
				$finder->files()->in($logDir);
				if($finder->hasResults()) {
					$finderResult = $finder->files()->getIterator();
				}
			} catch (\Exception $e) {
				$ret['error'] = $e->getMessage();
			}
		}

		foreach ($finderResult as $file) {
			if(!($file instanceof SplFileInfo)) {
				continue;
			}

			$fileName = $file->getFilename();

			$ret['files'][] = [
				'fileName' => $fileName,
				'fileUrl' => $this->generateUrl('app_admin_show_log', ['logFile' => $fileName]),
				'fileDownloadUrl' => $this->generateUrl('app_admin_download_log', ['logFile' => $fileName]),
				'fileSize' => round($file->getSize() / 1000000, 2)
			];
		}

		return $this->render('admin/log_files.html.twig', $ret);
	}

	#[Route(path: '/show-log/{logFile}', name: 'app_admin_show_log')]
	public function showLog(DirectoryFileRegister $directoryFileRegister, string $logFile): Response {
		$fileName = $this->validateLogFile($directoryFileRegister, $logFile);
		$content = '';

		if(!empty($fileName)) {
			$content = file_get_contents($fileName);
		}

		return $this->render('admin/log_file.html.twig', ['content' => $content]);
	}

	#[Route(path: '/download-log/{logFile}', name: 'app_admin_download_log')]
	public function downloadLog(DirectoryFileRegister $directoryFileRegister, string $logFile): Response {
		$fileName = $this->validateLogFile($directoryFileRegister, $logFile);

		if(!empty($fileName)) {
			$response = $this->file($fileName, $logFile);
			$response->headers->set('Content-type', 'text');

			return $response;
		}

		return new Response('Datei nicht gefunden');
	}

	private function validateLogFile(DirectoryFileRegister $directoryFileRegister, string $logFile): string {
		$fileSystem = new Filesystem();
		$logDir = $directoryFileRegister->getLogDir();
		$fileName = $logDir . '/' . $logFile;

		if($fileSystem->exists($fileName)) {
			return $fileName;
		}

		return '';
	}

	#[Route(path: '/php-info', name: 'app_admin_php_info')]
	public function phpInfo(): Response {
		return new Response(phpinfo(INFO_GENERAL | INFO_CONFIGURATION | INFO_MODULES ));
	}

  #[Route(path: '/files/show', name: 'app_admin_show_files')]
  public function showFiles(FileRepository $fileRepository): Response {
    $files = $fileRepository->findAll();

    $ret['files'] = [];
    foreach ($files as $file) {
      $ret['files'][] = [
        'id' => $file->getId(),
        'name' => $file->getFilename(),
        'url' => $this->generateUrl('app_file_download', ['fileId' => $file->getId()]),
      ];
    }

    return $this->render('admin/files.html.twig', $ret);
  }

}
