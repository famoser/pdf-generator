<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Tests\Integration\Frontend\LinearDocument;

use PdfGenerator\Frontend\Content\Paragraph;
use PdfGenerator\Frontend\Content\Rectangle;
use PdfGenerator\Frontend\Content\Style\DrawingStyle;
use PdfGenerator\Frontend\Content\Style\TextStyle;
use PdfGenerator\Frontend\Layout\ContentBlock;
use PdfGenerator\Frontend\Layout\Flow;
use PdfGenerator\Frontend\Layout\Style\FlowDirection;
use PdfGenerator\Frontend\LinearDocument;
use PdfGenerator\Frontend\Resource\Font;

class FlowTestCase extends LinearDocumentTestCase
{
    public function testPrintFlowContent(): void
    {
        // arrange
        $document = new LinearDocument();

        // act
        $rectangleStyle = new DrawingStyle(0.25);
        $flow = new Flow(FlowDirection::COLUMN);
        for ($i = 0; $i < 800; ++$i) {
            $rectangle = new Rectangle($rectangleStyle);
            $contentBlock = new ContentBlock($rectangle);
            $contentBlock->setWidth($i * 5 % 40);
            $contentBlock->setHeight($i * 3 % 17);
            $flow->add($contentBlock);
        }
        $outerFlow = new Flow();
        $outerFlow->add($flow);
        $document->add($outerFlow);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('1 0 0 1 0 -6 cm 0 0 10 6 re s', $result);
        $this->assertStringContainsString('1 0 0 1 0 -6 cm 0 0 30 6 re s', $result);
        $this->assertStringContainsString('1 0 0 1 0 -3 cm 0 0 10 3 re s', $result);
        $this->assertStringContainsString('1 0 0 1 35 258 cm 0 0 30 7 re s', $result);
    }

    public function testPrintFlowText(): void
    {
        // arrange
        $document = new LinearDocument();

        $font = Font::createFromDefault();
        $normalText = new TextStyle($font, 3);

        // act
        /** @var Paragraph[] $paragraphs */
        $paragraphs = [];
        $paragraph = new Paragraph();
        $paragraph->add($normalText, 'PDF ist ein Textformat, strukturiert ähnlich wie XML, einfach etwas weniger Struktur. ');
        $paragraph->add($normalText, 'Am besten einmal ein kleines PDF im Texteditor öffnen und durchschauen. Zum Beispiel vom ');
        $paragraph->add($normalText, 'Kontoauszug');
        $paragraph->add($normalText, ', diese PDFs haben oft etwas weniger komischer binary Anteil wie dies z.B. Tex generierte Dokumente haben.');
        $paragraphs[] = $paragraph;

        $paragraph = new Paragraph();
        $paragraph->add($normalText, 'Es würde mich nicht erstaunen, wenn das meiste über das Format von solchen simplen PDFs selber zusammengereimt werden kann: Abgesehen von den Auswüchsen wie Formulare oder Schriftarten ist es nämlich ganz schön simpel gehalten. ');
        $paragraph->add($normalText, 'Der Parser muss eigentlich nur Dictionaries (key-value Datenstruktur) und Streams (binary blobs) verstehen. ');
        $paragraph->add($normalText, 'Das ist praktisch: Die meisten PDFs Dateien sind streng genommen fehlerhaft generiert, und in dem die Parsers nur diese beiden Objekte unterscheiden müssen, können trotzdem die allermeisten PDFs angezeigt werden. ');
        $paragraph->add($normalText, 'Die meisten Readers sind auch ganz gut darin; schliesslich gibt der Nutzer dem PDF-Viewer Schuld, wenn etwas nicht funktioniert, und nicht dem Generator.');
        $paragraphs[] = $paragraph;

        $paragraph = new Paragraph();
        $paragraph->add($normalText, 'Eine Abstraktionsebene höher gibt es dann einen Header (die PDF Version), einen Trailer mit der Cross Reference Table (Byte Offsets zu den verschiedenen Teilen des PDFs) und den Body (mit dem ganzen Inhalt). ');
        $paragraph->add($normalText, 'Die Cross Reference Table war früher einmal nützlich, um die relevanten Teile des PDFs schnell anzuzeigen. ');
        $paragraph->add($normalText, 'Bei aktuellen Readers wird diese Sektion aber vermutlich ignoriert; auch komplett falsche Werte haben keinen Einfluss auf die Darstellung. ');
        $paragraph->add($normalText, 'Als Inhaltsarten gibt es nenneswerterweise Bilder, Text und Schriftarten. ');
        $paragraph->add($normalText, 'Jeder dieser Inhalte ist an eine jeweilige "Page" gebunden, mit spezifizierten x/y Koordinaten. ');
        $paragraph->add($normalText, 'Ganz nach PDF-Konzept gibts hier keine magic: Alle Angaben sind absolut und keine automatische Zentrierung oder Skalierung wird angeboten.');
        $paragraphs[] = $paragraph;

        $outerFlow = new Flow(FlowDirection::ROW, 10);
        $flow = new Flow(FlowDirection::COLUMN);
        $flow->setWidth(85);
        for ($i = 0; $i < 30; ++$i) {
            foreach ($paragraphs as $paragraph) {
                $contentBlock = new ContentBlock($paragraph);
                $contentBlock->setMargin([0, 3 * 1.6, 0, 0]);
                $flow->add($contentBlock);
            }
        }
        $outerFlow->add($flow);
        $document->add($outerFlow);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('1 0 0 1 0 -60.96 cm BT', $result);
        $this->assertStringContainsString('(Jeder dieser)Tj (Inhalte', $result);
        $this->assertStringContainsString('1 0 0 1 0 -60.96 cm BT (Eine', $result);
    }
}
