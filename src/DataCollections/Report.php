<?php

namespace App\DataCollections;

use App\DataCollections\ReportElements\ReportElementInterface;
use App\DataCollections\ReportElements\ReportHeader;
use App\DataCollections\ReportElements\ReportSection;

class Report {

  private array $reportBody = [];
  private ReportHeader $reportHeader;
  private bool $tableOfContent = false;

  public function getReportBody(): array {
    $ret = [];

    foreach ($this->reportBody as $sectionData) {
      $sectionId = $sectionData->getId();
      $ret[$sectionId] = $sectionData->toArray();
      $ret[$sectionId]['children'] = [];
      foreach ($sectionData->iterateChildren() as $element) {
        if($element instanceof ReportElementInterface && $element->isValid()) {
          $data = $element->getElementData();
        } elseif(!is_array($element)) {
          $data = [];
        }

        if(empty($data)) {
          continue;
        }

        $ret[$sectionId]['children'][] = $data;
      }
    }

    return $ret;
  }

  public function setReportHeader(ReportHeader $reportHeader): Report {
    $this->reportHeader = $reportHeader;
    return $this;
  }

  public function getReportHeader(): ReportHeader {
    return $this->reportHeader;
  }

  public function hasTableOfContent(): bool {
    return $this->tableOfContent;
  }

  public function setTableOfContent(bool $tableOfContent): Report {
    $this->tableOfContent = $tableOfContent;
    return $this;
  }

  public function createSection(string $headline): ReportSection {
    $id = array_key_last($this->reportBody);
    $id = $id === null ? 0 : $id + 1;
    $section = new ReportSection($id, $headline);
    $this->reportBody[] = $section;
    return $section;
  }

  public function addElement(int $sectionId, string $term, string | array $messages = []): Report {
    if(!isset($this->reportBody[$sectionId])) {
      return $this;
    }

    if(!is_array($messages)) {
      $messages = [$messages];
    }

    $this->reportBody[$sectionId]['children'][] = [
      'type' => 'element',
      'term' => $term,
      'messages' => $messages
    ];

    return $this;
  }

  public function getAsArray(): array {
    return [
      'header' => $this->getReportHeader()->getElementData(),
      'body' => $this->getReportBody(),
      'tableOfContent' =>	$this->hasTableOfContent()
    ];
  }

}
