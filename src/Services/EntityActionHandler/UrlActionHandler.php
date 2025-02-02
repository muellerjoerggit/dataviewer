<?php

namespace App\Services\EntityActionHandler;

use App\DaViEntity\EntityInterface;
use App\Services\ClientService;
use App\Services\EntityAction\EntityActionConfigAttrInterface;
use App\Services\EntityAction\EntityActionHandlerInterface;
use App\Services\EntityAction\UrlEntityActionHandlerInterface;
use App\Services\Placeholder\PlaceholderService;

class UrlActionHandler implements EntityActionHandlerInterface, UrlEntityActionHandlerInterface {

  public function __construct(
    private readonly PlaceholderService $placeholderService,
    private readonly ClientService $clientService,
  ) {}

  public function generateUrl(EntityActionConfigAttrInterface $config, EntityInterface $entity): array {
    if(!$config instanceof UrlActionConfigAttr || !$config->isValid()) {
      return [];
    }

    $url = $this->placeholderService->prepareInsertPlaceholders($config, $entity, $config->getUrlTemplate());

    if(!$config->isExternalUrl()) {
      $clientUrl = $this->clientService->getClientUrl($entity->getClient());
      $clientUrl = str_ends_with($clientUrl, '/') ? $clientUrl : $clientUrl . '/';
      $url = empty($clientUrl) ? '/' . $url : $clientUrl . $url;
    } else {
      $url = str_starts_with($url, 'http') ? $url : 'https://' . $url;
    }

    return [
      'component' => 'UrlAction',
      'data' => [
        'url' => $url,
        'title' => $config->getTitle(),
        'description' => $config->getDescription()
      ]
    ];
  }

}
