<?php

namespace App\Services\Validation;

use App\DaViEntity\Schema\EntityTypesRegister;
use App\Services\DirectoryFileRegister;

class ErrorCodesRegister {

	private array $errorCodes = [];
	private array $entityTypeAbbreviation = [];

	public function __construct(
    private readonly EntityTypesRegister $entityTypesRegister,
    private readonly DirectoryFileRegister $directoryFileRegister
	){
		$this->init();
	}

	public function getAbbreviationByEntityType(string $entityType): string {
		return $this->entityTypeAbbreviation[$entityType] ?? '';
	}

	public function iterateErrorCodesByEntityType(string $entityType): \Generator {
		$abbreviation = $this->entityTypeAbbreviation[$entityType] ?? '';

		if(empty($abbreviation)) {
			yield from [];
			return;
		}

		foreach ($this->errorCodes[$abbreviation] as $suffix => $errorCode) {
			yield $suffix => $errorCode;
		}
	}

	public function getErrorDefinitionByCode(string $code): array {
		[$prefix, $suffix] = $this->explodePreSuffix($code);
		return $this->getErrorDefinition($prefix, $suffix);
	}

	public function getErrorDefinition(string $prefix, int $suffix): array {
		if(!$this->hasErrorDefinition($prefix, $suffix)) {
			return [
				'message' => 'unbekannter Fehler',
				'description' => 'keine Fehlerdefinition vorhanden',
				'categories' => [],
				'placeholder' => [],
				'level' => 'info'
			];
		}

		return $this->errorCodes[$prefix][$suffix];
	}

	private function hasErrorDefinition(string $prefix, int $suffix): bool {
		return isset($this->errorCodes[$prefix][$suffix]);
	}

	private function explodePreSuffix($code): array {
		[$prefix, $suffix] = explode('-', $code);
		$suffix = (int)$suffix;
		return [$prefix, $suffix];
	}

	private function init(): void {
		foreach ($this->entityTypesRegister->iterateErrorCodesFiles() as $entityType => $errorCodesFile) {
			$deserialized = $this->directoryFileRegister->parseYamlFromFile($errorCodesFile->getRealPath());

			if(empty($deserialized)) {
				continue;
			}

			$abbreviation = $deserialized['abbreviation'];

			if(in_array($abbreviation, $this->entityTypeAbbreviation)) {
				continue;
			}

			$this->entityTypeAbbreviation[$entityType] = $abbreviation;
			$this->errorCodes[$abbreviation] = $deserialized['codes'] ?? [];
		}

		$commonErrorCodes = $this->directoryFileRegister->getCommonErrorCodes();
		$this->errorCodes['ALL'] = $commonErrorCodes['ALL']['codes'] ?? [];
		$this->errorCodes['INT'] = $commonErrorCodes['INTERN']['codes'] ?? [];
	}

}
