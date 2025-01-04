<?php

namespace App\Services;

use App\SymfonyEntity\File;
use App\SymfonyRepository\FileRepository;

class FileService {

	public const array MIME_TYPES = [
		'csv' => [
			'type' => 'text/csv',
			'suffix' => 'csv'
		]
	];

	public const int FILE_TYPE_EXPORT = 1;

	public function __construct(
    private readonly FileRepository $fileRepository,
    private readonly DirectoryFileRegister $directoryFileRegister
  ) {}

	public static function getMimeType(string $type): string {
		return self::MIME_TYPES[$type]['type'] ?? 'text/plain';
	}

	public function getFullFilePath(File | int $file): string {
		if(!($file instanceof File)) {
			$file = $this->getFile($file);
		}

		if(!$file) {
			return '';
		}

		$type = $file->getType();

		switch ($type) {
			case self::FILE_TYPE_EXPORT:
				return $this->directoryFileRegister->getTempDir() . '/' . $file->getServerFilename();
		}

		return '';
	}

	public function getFile(int $fileId): ?File {
		return $this->fileRepository->find($fileId);
	}

}
