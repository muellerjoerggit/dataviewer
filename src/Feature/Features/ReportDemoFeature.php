<?php

namespace App\Feature\Features;

use App\DataCollections\Color;
use App\DataCollections\Report;
use App\DataCollections\Report_Items\ReportResultIcon;
use App\DataCollections\ReportElements\ReportHeader;
use App\DataCollections\ReportElements\ReportInfoText;
use App\DataCollections\ReportElements\ReportPreformattedText;
use App\DataCollections\ReportElements_Charts\Bar;
use App\DataCollections\ReportElements_Charts\ReportChartBar;
use App\DataCollections\ReportElements_Charts\xAxis;
use App\DataCollections\ReportElements_Table\ReportTable;
use App\DataCollections\ReportElements_Table\ReportTableCell;
use App\DataCollections\ReportElements_Table\ReportTableHeader;
use App\DataCollections\ReportElements_Table\ReportTableRow;
use App\DataCollections\Report_Items\ReportBadgeItem;
use App\DataCollections\Report_Items\ReportResultItem;
use App\Feature\AbstractFeatureReport;
use App\Feature\FeatureDefinition;

#[FeatureDefinition(
  label: 'Demo',
  description: 'Demo für alle möglichen Features vom Report',
)]
class ReportDemoFeature extends AbstractFeatureReport {

  private const string DEMO_TEXT = 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.';
  private const string JSON_TEXT = <<<END
{
  "headline: "Lorem ipsum dolor sit amet",
  "text": "Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.",
  "paragraphs": [
    "Lorem ipsum dolor sit amet",
    "consetetur sadipscing elitr",
    "sed diam voluptua."
  ],
  list: [
    {
      "id": 1,
      "headline": "Lorem ipsum dolor sit amet",
      "text": "Sed diam voluptua."
    },
    {
      "id": 2,
      "headline": "Lorem ipsum dolor sit amet",
      "text": "Sed diam voluptua."
    },
    {
      "id": 3,
      "headline": "Lorem ipsum dolor sit amet",
      "text": "Sed diam voluptua."
    },
    {
      "id": 4,
      "headline": "Lorem ipsum dolor sit amet",
      "text": "Sed diam voluptua."
    }      
  ]
}
END;

  private const array WORDS = ['rebum','accusam','dolores','gubergren','takimata','sanctus','voluptua'];

  public function getReportList(string $client): array {
    $report = new Report();

    $report->setReportHeader(ReportHeader::create('Demo', 'Demo für den Report'));

    $tableSection = $report->createSection('Tabellen');

    $tableItems = ReportTable::create();
    $tableSection->addChild($tableItems);

    $tableItems->addHeader(
      ReportTableHeader::create('scalar', 'skalare Werte'),
      ReportTableHeader::create('badge', 'Badge'),
      ReportTableHeader::create('result', 'Resultat'),
      ReportTableHeader::create('result2', 'Resultat ohne Begriff'),
    );

    $tableItems->addRows(
      ReportTableRow::create(
        ReportTableCell::create('scalar', 1),
        ReportTableCell::create('badge', ReportBadgeItem::createDefault('normales Badge')),
        ReportTableCell::create('result', ReportResultItem::createFull(ReportResultIcon::SUCCESS, 'erfolgreich', '', 'zusätzliche Information', 'zusätzliche Infos zum Ergebnis')),
        ReportTableCell::create('result2', ReportResultItem::create(ReportResultIcon::SUCCESS)),
      ),
      ReportTableRow::create(
        ReportTableCell::create('scalar', 'text'),
        ReportTableCell::create('badge', ReportBadgeItem::createWarningBadge('Warnung')),
        ReportTableCell::create('result', ReportResultItem::create(ReportResultIcon::FAILURE, 'fehlgeschlagen')),
        ReportTableCell::create('result2', ReportResultItem::create(ReportResultIcon::FAILURE)),
      ),
      ReportTableRow::create(
        ReportTableCell::create('scalar', 'Lorem ipsum dolor sit amet'),
        ReportTableCell::create('badge', ReportBadgeItem::createOutlineBadge('Outline')),
        ReportTableCell::create('result', ReportResultItem::create(ReportResultIcon::UNKNOWN, 'unbekannt')),
        ReportTableCell::create('result2', ReportResultItem::create(ReportResultIcon::UNKNOWN)),
      ),
      ReportTableRow::create(
        ReportTableCell::create('scalar', 123),
        ReportTableCell::create('badge', ReportBadgeItem::createSecondaryBadge('Secondary')),
        ReportTableCell::create('result', ReportResultItem::create(ReportResultIcon::SUCCESS, 'erfolg')),
        ReportTableCell::create('result2', ReportResultItem::create(ReportResultIcon::SUCCESS)),
      ),
    );

    $tableScalar = ReportTable::create();
    $tableSection->addChild($tableScalar);

    $tableScalar
      ->addHeader(ReportTableHeader::create('firstColumn', ''))
      ->setFirstColumnSticky(true);

    for($r = 0; $r < 10; $r++) {
      $row = ReportTableRow::create();
      $row->addCell(ReportTableCell::create('firstColumn', 'Zeile Nr. ' . $r));
      for($c = 0; $c < 30; $c++) {
        $columnKey = 'cell' . $c;
        if($c % 3 == 0) {
          $wordKeys = array_rand(self::WORDS, rand(2, 7));
          $words = implode(' ', array_intersect_key(self::WORDS, array_flip($wordKeys)));
          $row->addCell(ReportTableCell::create($columnKey, $words));
        } else {
          $row->addCell(ReportTableCell::create($columnKey, rand(-1000, 1000)));
        }

      }
      $tableScalar->addRows($row);
    }

    for($h = 0; $h < 30; $h++) {
      $tableScalar->addHeader(
        ReportTableHeader::create('cell' . $h, 'Spalte Nr. ' . $h)
      );
    }

    $textSection = $report->createSection('Text');
    $textSection
      ->addChild(ReportInfoText::create(self::DEMO_TEXT))
      ->addChild(ReportPreformattedText::create(self::DEMO_TEXT))
      ->addChild(ReportPreformattedText::create(self::JSON_TEXT));

    $barChart = ReportChartBar::create(
      xAxis::create('month'),
      Bar::create('active', 'aktive Nutzer', Color::create(100, 150, 20)),
      Bar::create('new', 'neue Nutzer', Color::create(20, 150, 100)),
    );

    $dataPoints = [
      'jan' => 'Januar',
      'feb' => 'Februar',
      'mar' => 'März',
      'apr' => 'April',
      'may' => 'Mai',
      'jun' => 'Juni',
    ];

    foreach ($dataPoints as $key => $label) {
      $barChart->addDataPoint($key, $label, [
        'active' => rand(50, 100),
        'new' => rand(5, 10),
      ]);
    }

    $chartSection = $report->createSection('Chart');
    $chartSection
      ->addChild($barChart);

    return [$report];
  }


}