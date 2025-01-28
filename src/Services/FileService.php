<?php

namespace App\Services;

use App\SymfonyEntity\File;
use App\SymfonyRepository\FileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class FileService {

  public const string EXTENSION_CSV = 'csv';

	public const array MIME_TYPES = [
		self::EXTENSION_CSV => [
			'type' => 'text/csv',
			'suffix' => 'csv'
		]
	];

	public const int FILE_TYPE_EXPORT = 1;

	public function __construct(
    private readonly FileRepository $fileRepository,
    private readonly DirectoryFileRegister $directoryFileRegister,
    private readonly EntityManagerInterface $entityManager,
  ) {}

	public static function getMimeType(string $type): string {
		return self::MIME_TYPES[$type]['type'] ?? 'text/plain';
	}

	public function getFile(int $fileId): ?File {
		return $this->fileRepository->find($fileId);
	}

  public function dumpTempFile(string $fileName, int $fileType, string $extension, string $fileContent): File | null {
    $file = new File();

    $fullPath = $this->directoryFileRegister->getTempFileName($fileType);
    try {
      (new Filesystem())->dumpFile($fullPath, $fileContent);
    } catch (IOExceptionInterface $exception) {
      return null;
    }

    $file
      ->setFilename($fileName)
      ->setPath($fullPath)
      ->setType($fileType)
      ->setMimeType(self::getMimeType($extension));

    $this->entityManager->persist($file);
    $this->entityManager->flush();

    return $file;
  }

}
