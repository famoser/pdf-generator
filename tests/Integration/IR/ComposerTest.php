<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Tests\Integration\IR;

use PdfGenerator\Backend\Catalog\Font\Type0;
use PdfGenerator\Backend\Structure\Optimization\Configuration;
use PdfGenerator\IR\FlowPrinter;
use PdfGenerator\IR\Layout\SingleColumnLayout;
use PdfGenerator\IR\Structure\Document;
use PdfGenerator\IR\Structure\Document\Page\Content\Common\Color;
use PdfGenerator\IR\Structure\Document\Page\Content\Rectangle\RectangleStyle;
use PdfGenerator\IR\Structure\Document\Page\Content\Text\TextStyle;
use PdfGenerator\Tests\Resources\ResourcesProvider;
use PHPUnit\Framework\TestCase;

class ComposerTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testPrintTextTextInResultFile()
    {
        // arrange
        $text = 'hi mom';
        $document = new Document();
        $composer = new FlowPrinter($document, new SingleColumnLayout($document));

        // act
        $composer->printPhrase($text);
        $result = $document->render()->save();
        file_put_contents('pdf.pdf', $result);

        // assert
        $this->assertStringContainsString($text, $result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintTextMultipleTextsInResultFile()
    {
        // arrange
        $text = 'hi mom';
        $document = new Document();
        $composer = new FlowPrinter($document, new SingleColumnLayout($document));

        // act
        $composer->printPhrase($text . '1');
        $composer->printPhrase($text . '2');
        $result = $document->render()->save();
        file_put_contents('pdf.pdf', $result);

        // assert
        $this->assertStringContainsString($text . '1', $result);
        $this->assertStringContainsString($text . '2', $result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintTextCursorInResultFile()
    {
        // arrange
        $document = new Document();
        $composer = new FlowPrinter($document, new SingleColumnLayout($document));

        // act
        $composer->moveDown(10);
        $composer->printPhrase('text');
        $result = $document->render()->save();

        // assert
        $this->assertStringContainsString((string)(262), $result); // 297 - 30 - ascender
        $this->assertStringContainsString((string)(25), $result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintLineCursorInResultFile()
    {
        // arrange
        $width = 20;
        $height = 30;
        $document = new Document();
        $composer = new FlowPrinter($document, new SingleColumnLayout($document));

        $rectangleStyle = new RectangleStyle(0.5, Color::createFromHex('#aefaef'), Color::createFromHex('#abccba'));
        $composer->getPrinter()->getPrinter()->setRectangleStyle($rectangleStyle);

        // act
        $composer->printRectangle($width, $height);
        $composer->moveRight($width);
        $composer->printRectangle($width + $width, $height + $height);
        $result = $document->render()->save();

        // assert
        $this->assertStringContainsString((string)($width + $width), $result);
        $this->assertStringContainsString((string)($height + $height), $result);
        $this->assertStringContainsString((string)$width, $result);
        $this->assertStringContainsString((string)$height, $result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintTextDifferentDefaultFontTextAppears()
    {
        // arrange
        $document = new Document();
        $composer = new FlowPrinter($document, new SingleColumnLayout($document));

        $font = $document->getOrCreateDefaultFont(Document\Font\DefaultFont::FONT_TIMES, Document\Font\DefaultFont::STYLE_DEFAULT);
        $textStyle = new TextStyle($font, 30);
        $composer->getPrinter()->getPrinter()->setTextStyle($textStyle);

        // act
        $composer->printPhrase('hi mom');
        $result = $document->render()->save();
        file_put_contents('pdf.pdf', $result);

        // assert
        $this->assertNotEmpty($result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintImageImageAppears()
    {
        // arrange
        $imageSrc = ResourcesProvider::getImage1Path();
        $document = new Document();
        $printer = new FlowPrinter($document, new SingleColumnLayout($document));

        // act
        $image = $document->getOrCreateImage($imageSrc);
        $printer->printImage($image, 20, 20);
        $result = $document->render()->save();
        file_put_contents('pdf.pdf', $result);

        // assert
        $this->assertNotEmpty($result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintTextWithEmbeddedFontTextAppears()
    {
        // arrange
        $document = new Document();
        $composer = new FlowPrinter($document, new SingleColumnLayout($document));
        $font = $document->getOrCreateEmbeddedFont(ResourcesProvider::getFontOpenSansPath());
        $textStyle = new TextStyle($font, 12);

        // act
        $composer->getPrinter()->getPrinter()->setTextStyle($textStyle);
        $composer->printPhrase('Hallo und Sonderzéíchèn');
        $backend = $document->render();

        $documentConfiguration = new Configuration();
        $documentConfiguration->setCreateFontSubsets(true);
        $documentConfiguration->setAutoResizeImages(true);
        $backend->setConfiguration($documentConfiguration);

        $catalog = $backend->render();
        $result = $catalog->save();
        file_put_contents('pdf.pdf', $result);

        /** @var Type0 $font */
        $font = $catalog->getPages()->getKids()[0]->getResources()->getFonts()[0];
        $type0Font = $font->getDescendantFont()->getFontDescriptor()->getFontFile3()->getFontData();
        file_put_contents('subset.ttf', $type0Font);

        // assert
        $this->assertNotEmpty($result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintTextSizing()
    {
        // arrange
        $document = new Document();
        $composer = new FlowPrinter($document, new SingleColumnLayout($document));
        $font = $document->getOrCreateEmbeddedFont(ResourcesProvider::getFontOpenSansPath());
        $textStyle = new TextStyle($font, 5, 1.2);

        // act
        $composer->getPrinter()->getPrinter()->setTextStyle($textStyle);
        $loremIpsum = 'PDF ist ein Textformat, strukturiert ähnlich wie XML, einfach etwas weniger Struktur. Am besten einmal ein kleines PDF im Texteditor öffnen und durchschauen. Zum Beispiel vom Kontoauszug; diese PDFs haben oft etwas weniger komischer binary Anteil wie dies z.B. Tex generierte Dokumente haben. Es würde mich nicht erstaunen, wenn das meiste über das Format von solchen simplen PDFs selber zusammengereimt werden kann: Abgesehen von den Auswüchsen wie Formulare oder Schriftarten ist es nämlich ganz schön simpel gehalten. Der Parser muss eigentlich nur Dictionaries (key-value Datenstruktur) und Streams (binary blobs) verstehen. Das ist praktisch: Die meisten PDFs Dateien sind streng genommen fehlerhaft generiert, und in dem die Parsers nur diese beiden Objekte unterscheiden müssen, können trotzdem die allermeisten PDFs angezeigt werden. Die meisten Readers sind auch ganz gut darin; schliesslich gibt der Nutzer dem PDF-Viewer Schuld, wenn etwas nicht funktioniert, und nicht dem Generator. Eine Abstraktionsebene höher gibt es dann einen Header (die PDF Version), einen Trailer mit der Cross Reference Table (Byte Offsets zu den verschiedenen Teilen des PDFs) und den Body (mit dem ganzen Inhalt). Die Cross Reference Table war früher einmal nützlich, um die relevanten Teile des PDFs schnell anzuzeigen. Bei aktuellen Readers wird diese Sektion aber vermutlich ignoriert; auch komplett falsche Werte haben keinen Einfluss auf die Darstellung. Als Inhaltsarten gibt es nenneswerterweise Bilder, Text und Schriftarten. Jeder dieser Inhalte ist an eine jeweilige "Page" gebunden, mit spezifizierten x/y Koordinaten. Ganz nach PDF-Konzept gibts hier keine magic: Alle Angaben sind absolut und keine automatische Zentrierung oder Skalierung wird angeboten. Auch beim Text müssen so Umbrüche in einem Paragraph oder der Abstand zwischen den Buchstaben im Blocksatz explizit definiert werden. Wirklich toll wirds aber erst mit Schriftarten. Das PDF hat ganze 14 Standardschriftarten; es sind die allseits beliebten Times Roman, Courier und Helvetica, und ZapfDingbats und Symbol (Emojis bevors Emojis gab). Dazu gibts diverse Standard Ein-Byte Encodings; das brauchbarste für Europäer ist das WinAnsiEncoding. Für anspruchslose Kunden und deutsche, französische oder italienische Korrespondez mag man damit wegkommen. Ab dem ersten Smørrebrød ist aber Schluss: Dann muss man mit eigenen "Embedded Fonts" arbeiten.';
        $composer->printPhrase($loremIpsum);
        $composer->printPhrase($loremIpsum . ' ' . $loremIpsum . ' ' . $loremIpsum);
        $backend = $document->render();

        $catalog = $backend->render();
        $result = $catalog->save();
        file_put_contents('pdf.pdf', $result);

        /** @var Type0 $font */
        $font = $catalog->getPages()->getKids()[0]->getResources()->getFonts()[0];
        $type0Font = $font->getDescendantFont()->getFontDescriptor()->getFontFile3()->getFontData();
        file_put_contents('subset.ttf', $type0Font);

        // assert
        $this->assertNotEmpty($result);
    }
}
