<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Tests\Integration\Frontend;

use Famoser\PdfGenerator\Frontend\Content\Rectangle;
use Famoser\PdfGenerator\Frontend\Content\Style\DrawingStyle;
use Famoser\PdfGenerator\Frontend\Content\Style\TextStyle;
use Famoser\PdfGenerator\Frontend\Content\TextBlock;
use Famoser\PdfGenerator\Frontend\Document;
use Famoser\PdfGenerator\Frontend\Layout\Block;
use Famoser\PdfGenerator\Frontend\Layout\ContentBlock;
use Famoser\PdfGenerator\Frontend\Layout\Flow;
use Famoser\PdfGenerator\Frontend\Layout\Style\ElementStyle;
use Famoser\PdfGenerator\Frontend\Layout\Style\FlowDirection;
use Famoser\PdfGenerator\Frontend\Layout\Text;
use Famoser\PdfGenerator\Frontend\Layout\Text\Alignment;
use Famoser\PdfGenerator\Frontend\Resource\Font;
use Famoser\PdfGenerator\IR\Document\Content\Common\Color;
use Famoser\PdfGenerator\Tests\Integration\Frontend\TestUtils\Render;
use PHPUnit\Framework\TestCase;

class FlowTest extends TestCase
{
    use Render;

    public function testPrintFlowContent(): void
    {
        // arrange
        $document = new Document();

        // act
        $rectangleStyle = new DrawingStyle(0.25);
        $flow = new Flow(FlowDirection::COLUMN);
        for ($i = 0; $i < 800; ++$i) {
            $rectangle = new Rectangle($i * 5 % 40, $i * 3 % 17, $rectangleStyle);
            $contentBlock = new ContentBlock($rectangle);
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

    public function testPrintSimpleFlowText(): void
    {
        // arrange
        $document = new Document();

        $font = Font::createFromDefault();
        $normalText = new TextStyle($font);

        // act
        $paragraph = new Text(alignment: Alignment::ALIGNMENT_JUSTIFIED);
        $paragraph->addSpan('PDF ist ein Textformat, strukturiert ähnlich wie XML, einfach etwas weniger Struktur. ', $normalText);
        $paragraph->addSpan('Am besten einmal ein kleines PDF im Texteditor öffnen und durchschauen. Zum Beispiel vom ', $normalText);
        $paragraph->addSpan('Kontoauszug', $normalText);
        $paragraph->addSpan(', diese PDFs haben oft etwas weniger komischer binary Anteil wie dies z.B. Tex generierte Dokumente haben.', $normalText);

        $flow = new Flow(FlowDirection::COLUMN);
        $flow->setWidth(85);
        $contentBlock = new Block($paragraph);
        $contentBlock->setMargin([0, 3 * 1.6, 0, 0]);
        $flow->add($contentBlock);
        $document->add($flow);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('0.533 Tw 5.76 TL (PDF ist e', $result);
        $this->assertStringContainsString('en. Zum Beispiel vom)"', $result);
    }

    public function testPrintFlowText(): void
    {
        // arrange
        $document = new Document(margin: 5);

        $font = Font::createFromDefault();
        $normalText = new TextStyle($font);

        // act
        /** @var TextBlock[] $paragraphs */
        $paragraphs = [];
        $paragraph = new Text(alignment: Alignment::ALIGNMENT_JUSTIFIED);
        $paragraph->addSpan('PDF ist ein Textformat, strukturiert ähnlich wie XML, einfach etwas weniger Struktur. ', $normalText);
        $paragraph->addSpan('Am besten einmal ein kleines PDF im Texteditor öffnen und durchschauen. Zum Beispiel vom ', $normalText);
        $paragraph->addSpan('Kontoauszug', $normalText);
        $paragraph->addSpan(', diese PDFs haben oft etwas weniger komischer binary Anteil wie dies z.B. Tex generierte Dokumente haben.', $normalText);
        $paragraphs[] = $paragraph;

        $paragraph = new Text(alignment: Alignment::ALIGNMENT_RIGHT);
        $paragraph->addSpan('Es würde mich nicht erstaunen, wenn das meiste über das Format von solchen simplen PDFs selber zusammengereimt werden kann: Abgesehen von den Auswüchsen wie Formulare oder Schriftarten ist es nämlich ganz schön simpel gehalten. ', $normalText);
        $paragraph->addSpan('Der Parser muss eigentlich nur Dictionaries (key-value Datenstruktur) und Streams (binary blobs) verstehen. ', $normalText);
        $paragraph->addSpan('Das ist praktisch: Die meisten PDFs Dateien sind streng genommen fehlerhaft generiert, und in dem die Parsers nur diese beiden Objekte unterscheiden müssen, können trotzdem die allermeisten PDFs angezeigt werden. ', $normalText);
        $paragraph->addSpan('Die meisten Readers sind auch ganz gut darin; schliesslich gibt der Nutzer dem PDF-Viewer Schuld, wenn etwas nicht funktioniert, und nicht dem Generator.', $normalText);
        $paragraphs[] = $paragraph;

        $paragraph = new Text(alignment: Alignment::ALIGNMENT_CENTER);
        $paragraph->addSpan('Eine Abstraktionsebene höher gibt es dann einen Header (die PDF Version), einen Trailer mit der Cross Reference Table (Byte Offsets zu den verschiedenen Teilen des PDFs) und den Body (mit dem ganzen Inhalt). ', $normalText);
        $paragraph->addSpan('Die Cross Reference Table war früher einmal nützlich, um die relevanten Teile des PDFs schnell anzuzeigen. ', $normalText);
        $paragraph->addSpan('Bei aktuellen Readers wird diese Sektion aber vermutlich ignoriert; auch komplett falsche Werte haben keinen Einfluss auf die Darstellung. ', $normalText);
        $paragraphs[] = $paragraph;

        $paragraph = new Text(alignment: Alignment::ALIGNMENT_LEFT);
        $paragraph->addSpan('Als Inhaltsarten gibt es nenneswerterweise Bilder, Text und Schriftarten. ', $normalText);
        $paragraph->addSpan('Jeder dieser Inhalte ist an eine jeweilige "Page" gebunden, mit spezifizierten x/y Koordinaten. ', $normalText);
        $paragraph->addSpan('Ganz nach PDF-Konzept gibts hier keine magic: Alle Angaben sind absolut und keine automatische Zentrierung oder Skalierung wird angeboten.', $normalText);
        $paragraphs[] = $paragraph;

        $outerFlow = new Flow(FlowDirection::ROW, 4);
        $flow = new Flow(FlowDirection::COLUMN);
        $flow->setWidth(98);
        for ($i = 0; $i < 30; ++$i) {
            foreach ($paragraphs as $paragraph) {
                $paragraph->setMargin([0, 0, 0, 3 * 1.6]);
                $paragraph->setStyle(new ElementStyle(backgroundColor: new Color(200, 300, 0)));
                $flow->add($paragraph);
            }
        }
        $outerFlow->add($flow);
        $document->add($outerFlow);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('0.821 Tw 5.76 TL (PDF ist ein Te', $result);
        $this->assertStringContainsString('15.348 -5.76 TD (das Format von solchen simplen', $result);
        $this->assertStringContainsString('5.34 -5.76 TD (ganz gut darin; schliesslich gibt', $result);
    }
}
