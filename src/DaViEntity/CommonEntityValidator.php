<?php

namespace App\DaViEntity;

use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\Item\ItemHandler_Validator\ValidatorItemHandlerLocator;
use App\Logger\Logger;
use App\Logger\LogItems\LogItemInterface;
use App\Logger\LogItems\ValidationLogItem;
use App\Services\Validation\ErrorCodes;

class CommonEntityValidator implements EntityValidatorInterface {

  public const VALIDATE_ENTITY_TYPE = 'valType';

  public function __construct(
    protected readonly DaViEntityManager $entityManager,
    protected readonly ValidatorItemHandlerLocator $validatorHandlerLocator,
    protected readonly ErrorCodes $errorCodes,
    protected readonly EntityTypeSchemaRegister $schemaRegister,
    protected readonly Logger $logger
  ) {}

  public function validateEntity(EntityInterface $entity, array $options = []): void {
    $options = $this->buildOptions($options);
    $this->validateEntityInternal($entity, $options);
    if ($options[self::VALIDATE_ENTITY_TYPE]) {
      $this->validateEntityType($entity->getClient(), $entity);
    }
    $this->postValidateEntity($entity, $options);
  }



  protected function buildOptions(array $options): array {
    return array_merge([
      self::VALIDATE_ENTITY_TYPE => TRUE,
    ],
      $options
    );
  }

  protected function validateEntityInternal(EntityInterface $entity, array $options): void {
    $schema = $entity->getSchema();

    foreach ($schema->iterateProperties() as $property => $config) {
      if ($entity->hasPropertyItem($property)) {
        $item = $entity->getPropertyItem($property);
        $itemConfiguration = $item->getConfiguration();
      } else {
        continue;
      }

      if ($itemConfiguration->hasValidatorHandlerDefinition()) {
        foreach ($itemConfiguration->iterateValidatorHandlers() as $validatorHandler => $handlerSettings) {
          $handler = $this->validatorHandlerLocator->getHandler($validatorHandler);
          $handler->validateItemFromGivenEntity($entity, $property);
        }
      }
    }
  }

  public function validateEntityType(string $client, EntityInterface|null $entity = NULL): void {}

  protected function postValidateEntity(EntityInterface $entity, array $options): void {}

  protected function appendValidationLogItems(EntityInterface $targetEntity, EntityInterface $sourceEntity): void {
    $logItems = $sourceEntity->getAllLogsByLogType(ValidationLogItem::class);
    $targetEntity->addLogs($logItems);
  }

  protected function log(EntityInterface $entity, string $message, string $level, string $title = 'Validierung'): void {
    $logItem = ValidationLogItem::createLogItem($message, $title, $level);
    $this->logger->addLog($logItem);
    $entity->addLogs($logItem);
  }

  protected function logByMultipleCodes(EntityInterface $entity, array $codes): void {
    foreach ($codes as $code) {
      if (!isset($code['logCode'])) {
        continue;
      }
      $this->logByCode($entity, $code['logCode']);

      if (isset($code['logProperty'])) {
        $this->setItemError($entity, $code['logProperty'], $code['logCode']);
      }
    }
  }

  protected function logByCode(EntityInterface $entity, string $code): void {
    $error = $this->errorCodes->buildError($entity, $code);
    $logItem = ValidationLogItem::createValidationLogItem($error['message'], $error['level'], $code);
    $this->logger->addLog($logItem);
    $entity->addLogs($logItem);
  }

  protected function setItemError(EntityInterface $entity, string $property, string $code): void {
    $level = $this->errorCodes->getErrorLevel($code);
    $item = $entity->getPropertyItem($property);

    if (in_array($level, LogItemInterface::RED_LOG_LEVELS)) {
      $item->setRedError(TRUE);
    } elseif (in_array($level, LogItemInterface::YELLOW_LOG_LEVELS)) {
      $item->setYellowError(TRUE);
    }
  }

  protected function logAndItemError(EntityInterface $entity, string $code, string $property): void {
    $this->logByCode($entity, $code);
    $this->setItemError($entity, $property, $code);
  }

}
