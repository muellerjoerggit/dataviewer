<?php

namespace App\DataCollections\ReportElements;

class ReportInfoText extends AbstractReportElement {

  private string $message;

  public static function create(string $message): ReportInfoText {
    $infoText = new static();
    $infoText->setMessage($message);
    return $infoText;
  }

  public function getElementData(): array {
    return [
      'type' => 'infotext',
      'message' => $this->message
    ];
  }

  public function setMessage(string $message): ReportInfoText {
    $this->message = $message;
    return $this;
  }

  public function isValid(): bool {
    return !empty($this->message);
  }

}