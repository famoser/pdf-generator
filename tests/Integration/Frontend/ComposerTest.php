<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Tests\Integration\Frontend;

use PdfGenerator\Backend\Catalog\Font\Type0;
use PdfGenerator\FrontendResources\CursorPrinter\Buffer\RowBuffer;
use PdfGenerator\FrontendResources\CursorPrinter\Buffer\TableBuffer;
use PdfGenerator\FrontendResources\CursorPrinter\Buffer\TextBuffer;
use PdfGenerator\FrontendResources\CursorPrinter\CursorPrinter;
use PdfGenerator\FrontendResources\CursorPrinter\Layout\Column\SingleColumnGenerator;
use PdfGenerator\FrontendResources\CursorPrinter\Layout\ColumnLayout;
use PdfGenerator\IR\Document;
use PdfGenerator\IR\Document\Content\Common\Color;
use PdfGenerator\IR\Document\Content\Rectangle\RectangleStyle;
use PdfGenerator\IR\Document\Content\Text\TextStyle;
use PdfGenerator\Tests\Resources\ResourcesProvider;

class ComposerTest
{
    /**
     * @throws \Exception
     */
    public function testPrintTextTextInResultFile()
    {
        // arrange
        $document = new Document();
        $layout = $this->createSingleColumnLayout($document);
        $textWriter = new TextBuffer();
        $textStyle = $this->createBodyTextStyle($document);

        // act
        $textWriter->add($textStyle, 'hi mom');
        $paragraph = $textWriter->getMeasuredParagraph();
        $layout->add($textWriter);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('hi mom', $result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintMultipleTextMultipleTextsInResultFile()
    {
        // arrange
        $document = new Document();
        $layout = $this->createSingleColumnLayout($document);
        $textWriter = new TextBuffer();
        $textStyle = $this->createBodyTextStyle($document);

        // act
        $textWriter->add($textStyle, 'hi mom1'."\n");
        $textWriter->add($textStyle, 'hi mom2');
        $layout->addParagraph($textWriter);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('hi mom1', $result);
        $this->assertStringContainsString('hi mom2', $result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintRectangleRectangleInResultFile()
    {
        // arrange
        $document = new Document();
        $layout = $this->createSingleColumnLayout($document);
        $rectangleStyle = $this->createRectangleStyle();

        // act
        $layout->addRectangle($rectangleStyle, 20, 30);
        $layout->addRectangle($rectangleStyle, 40, 30);
        $layout->addRectangle($rectangleStyle, 100, 20);
        $layout->addRectangle($rectangleStyle, 10, 50);
        for ($i = 0; $i < 100; ++$i) {
            $layout->addRectangle($rectangleStyle, ($i * 50) % 70, 40);
        }

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('20', $result);
        $this->assertStringContainsString('30', $result);
        $this->assertStringContainsString('40', $result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintMultipleTextStylesTextInResultFile()
    {
        // arrange
        $document = new Document();
        $layout = $this->createSingleColumnLayout($document);
        $headerTextStyle = $this->createHeaderTextStyle($document);
        $bodyTextStyle = $this->createBodyTextStyle($document);
        $bodyBoldTextStyle = $this->createBodyBoldTextStyle($document);

        // act
        $textWriter = new TextBuffer();
        $textWriter->add($headerTextStyle, 'Integration of UTF-8'."\n");
        $layout->addParagraph($textWriter);

        $textWriter = new TextBuffer();
        $textWriter->add($bodyTextStyle, 'When you want to integrate all kinds of characters, there is little way around ');
        $textWriter->add($bodyBoldTextStyle, 'so-called UTF-8');
        $textWriter->add($bodyTextStyle, '. Even if used only in Europe, special characters ensure this is a capability in dire need.');
        $layout->addParagraph($textWriter, 20);

        $textWriter = new TextBuffer();
        $textWriter->add($bodyTextStyle, ' However, integrating UTF-8 also requires embedding TrueType fonts, and now things start to get complicated.');
        $layout->continueParagraph($textWriter, 10);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('Integration', $result);
        $this->assertStringContainsString('integrate', $result);
        $this->assertStringContainsString('so-called', $result);
        $this->assertStringContainsString('Europe', $result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintImagesImagesAppear()
    {
        // arrange
        $document = new Document();
        $layout = $this->createSingleColumnLayout($document);
        $imageSrc = ResourcesProvider::getImage1Path();
        $imageType = ResourcesProvider::getImage1Type();

        // act
        $image = $document->getOrCreateImage($imageSrc, $imageType);
        $layout->addImage($image, 30, 30);
        $layout->addImage($image, 100, 20);
        $layout->addImage($image, 40, 40);
        for ($i = 0; $i < 100; ++$i) {
            $layout->addImage($image, ($i * 50) % 70, 40);
        }

        // assert
        $result = $this->render($document);
        file_put_contents('pdf.pdf', $result);
        $this->assertNotEmpty($result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintTextWithEmbeddedFontTextAppears()
    {
        // arrange
        $document = new Document();
        $layout = $this->createSingleColumnLayout($document);
        $textWriter = new TextBuffer();
        $font = $document->getOrCreateEmbeddedFont(ResourcesProvider::getFontOpenSansPath());
        $textStyle = new TextStyle($font, 5, 1.2);

        // act
        $textWriter->add($textStyle, 'When you want to integrate all kinds of characters, there is little way around UTF-8. Custom font require you to specify an encoding anyways; why not just make it UTF-8?');
        $layout->addParagraph($textWriter, 20);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('integrate', $result);
        $this->assertStringContainsString('make', $result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintMultipleTextParagraphs()
    {
        // arrange
        $document = new Document();
        $layout = $this->createSingleColumnLayout($document);
        $headerTextStyle = $this->createHeaderTextStyle($document);
        $bodyTextStyle = $this->createBodyTextStyle($document);
        $bodyBoldTextStyle = $this->createBodyBoldTextStyle($document);

        // act
        $textWriter = new TextBuffer();
        $textWriter->add($headerTextStyle, 'PDF');
        $layout->addParagraph($textWriter);
        $layout->addSpace(5);

        $textWriter = new TextBuffer();
        $textWriter->add($bodyTextStyle, 'PDF ist ein Textformat, strukturiert ähnlich wie XML, einfach etwas weniger Struktur. ');
        $textWriter->add($bodyTextStyle, 'Am besten einmal ein kleines PDF im Texteditor öffnen und durchschauen. Zum Beispiel vom ');
        $textWriter->add($bodyBoldTextStyle, 'Kontoauszug');
        $textWriter->add($bodyTextStyle, ', diese PDFs haben oft etwas weniger komischer binary Anteil wie dies z.B. Tex generierte Dokumente haben.');
        $layout->addParagraph($textWriter);
        $layout->addSpace(3);

        $textWriter = new TextBuffer();
        $textWriter->add($bodyTextStyle, 'Es würde mich nicht erstaunen, wenn das meiste über das Format von solchen simplen PDFs selber zusammengereimt werden kann: Abgesehen von den Auswüchsen wie Formulare oder Schriftarten ist es nämlich ganz schön simpel gehalten. ');
        $textWriter->add($bodyTextStyle, 'Der Parser muss eigentlich nur Dictionaries (key-value Datenstruktur) und Streams (binary blobs) verstehen. ');
        $textWriter->add($bodyTextStyle, 'Das ist praktisch: Die meisten PDFs Dateien sind streng genommen fehlerhaft generiert, und in dem die Parsers nur diese beiden Objekte unterscheiden müssen, können trotzdem die allermeisten PDFs angezeigt werden. ');
        $textWriter->add($bodyTextStyle, 'Die meisten Readers sind auch ganz gut darin; schliesslich gibt der Nutzer dem PDF-Viewer Schuld, wenn etwas nicht funktioniert, und nicht dem Generator.');
        $layout->addParagraph($textWriter);
        $layout->addSpace(3);

        $textWriter = new TextBuffer();
        $textWriter->add($bodyTextStyle, 'Eine Abstraktionsebene höher gibt es dann einen Header (die PDF Version), einen Trailer mit der Cross Reference Table (Byte Offsets zu den verschiedenen Teilen des PDFs) und den Body (mit dem ganzen Inhalt). ');
        $textWriter->add($bodyTextStyle, 'Die Cross Reference Table war früher einmal nützlich, um die relevanten Teile des PDFs schnell anzuzeigen. ');
        $textWriter->add($bodyTextStyle, 'Bei aktuellen Readers wird diese Sektion aber vermutlich ignoriert; auch komplett falsche Werte haben keinen Einfluss auf die Darstellung. ');
        $textWriter->add($bodyTextStyle, 'Als Inhaltsarten gibt es nenneswerterweise Bilder, Text und Schriftarten. ');
        $textWriter->add($bodyTextStyle, 'Jeder dieser Inhalte ist an eine jeweilige "Page" gebunden, mit spezifizierten x/y Koordinaten. ');
        $textWriter->add($bodyTextStyle, 'Ganz nach PDF-Konzept gibts hier keine magic: Alle Angaben sind absolut und keine automatische Zentrierung oder Skalierung wird angeboten.');
        $layout->addParagraph($textWriter);
        $layout->addSpace(3);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('Texteditor', $result);
        $this->assertStringContainsString('Parser', $result);
        $this->assertStringContainsString('Abstraktionsebene', $result);
        $this->assertStringContainsString('magic', $result);
    }

    /**
     * @throws \Exception
     */
    public function testPrintTextOverMultiplePages()
    {
        // arrange
        $document = new Document();
        $layout = $this->createSingleColumnLayout($document);
        $textStyle = $this->createBodyTextStyle($document);

        // act
        $loremIpsum = 'PDF ist ein Textformat, strukturiert ähnlich wie XML, einfach etwas weniger Struktur. Am besten einmal ein kleines PDF im Texteditor öffnen und durchschauen. Zum Beispiel vom Kontoauszug; diese PDFs haben oft etwas weniger komischer binary Anteil wie dies z.B. Tex generierte Dokumente haben. Es würde mich nicht erstaunen, wenn das meiste über das Format von solchen simplen PDFs selber zusammengereimt werden kann: Abgesehen von den Auswüchsen wie Formulare oder Schriftarten ist es nämlich ganz schön simpel gehalten. Der Parser muss eigentlich nur Dictionaries (key-value Datenstruktur) und Streams (binary blobs) verstehen. Das ist praktisch: Die meisten PDFs Dateien sind streng genommen fehlerhaft generiert, und in dem die Parsers nur diese beiden Objekte unterscheiden müssen, können trotzdem die allermeisten PDFs angezeigt werden. Die meisten Readers sind auch ganz gut darin; schliesslich gibt der Nutzer dem PDF-Viewer Schuld, wenn etwas nicht funktioniert, und nicht dem Generator. Eine Abstraktionsebene höher gibt es dann einen Header (die PDF Version), einen Trailer mit der Cross Reference Table (Byte Offsets zu den verschiedenen Teilen des PDFs) und den Body (mit dem ganzen Inhalt). Die Cross Reference Table war früher einmal nützlich, um die relevanten Teile des PDFs schnell anzuzeigen. Bei aktuellen Readers wird diese Sektion aber vermutlich ignoriert; auch komplett falsche Werte haben keinen Einfluss auf die Darstellung. Als Inhaltsarten gibt es nenneswerterweise Bilder, Text und Schriftarten. Jeder dieser Inhalte ist an eine jeweilige "Page" gebunden, mit spezifizierten x/y Koordinaten. Ganz nach PDF-Konzept gibts hier keine magic: Alle Angaben sind absolut und keine automatische Zentrierung oder Skalierung wird angeboten. Auch beim Text müssen so Umbrüche in einem Paragraph oder der Abstand zwischen den Buchstaben im Blocksatz explizit definiert werden. Wirklich toll wirds aber erst mit Schriftarten. Das PDF hat ganze 14 Standardschriftarten; es sind die allseits beliebten Times Roman, Courier und Helvetica, und ZapfDingbats und Symbol (Emojis bevors Emojis gab). Dazu gibts diverse Standard Ein-Byte Encodings; das brauchbarste für Europäer ist das WinAnsiEncoding. Für anspruchslose Kunden und deutsche, französische oder italienische Korrespondez mag man damit wegkommen. Ab dem ersten Smørrebrød ist aber Schluss: Dann muss man mit eigenen "Embedded Fonts" arbeiten.';
        $loremIpsum6 = $loremIpsum.' '.$loremIpsum.' '.$loremIpsum.' '.$loremIpsum.' '.$loremIpsum.' '.$loremIpsum;
        $textWriter = new TextBuffer();
        $textWriter->add($textStyle, $loremIpsum);
        $textWriter->add($textStyle, ' '.$loremIpsum6);
        $layout->addParagraph($textWriter);
        $layout->addSpace(5);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('Kontoauszug', $result);
    }

    /**
     * @throws \Exception
     */
    public function ignoredTestPrintRows()
    {
        // arrange
        $document = new Document();
        $layout = $this->createSingleColumnLayout($document);
        $textStyle = $this->createBodyTextStyle($document);

        // act
        $tableBuffer = new TableBuffer();
        $rowBuffer = new RowBuffer();
        $textBuffer = new TextBuffer();
        $textBuffer->add($textStyle, 'Hallo Welt');
        $rowBuffer->add(0, $textBuffer);
        $rowBuffer->add(1, $textBuffer);
        $tableBuffer->add($rowBuffer);
        $tableBuffer->add($rowBuffer);
        $tableBuffer->add($rowBuffer);

        $layout->addTable($tableBuffer);
        $layout->addSpace(5);

        // assert
        $result = $this->render($document);
        $this->assertStringContainsString('Hallo Welt', $result);
    }

    private function createSingleColumnLayout(Document $document): ColumnLayout
    {
        $printer = new CursorPrinter($document);
        $columnGenerator = new SingleColumnGenerator($document);

        return new ColumnLayout($printer, $columnGenerator);
    }

    private function createBodyTextStyle(Document $document): TextStyle
    {
        $font = $document->getOrCreateDefaultFont(Document\Resource\Font\DefaultFont::FONT_TIMES, Document\Resource\Font\DefaultFont::STYLE_DEFAULT);

        return new TextStyle($font, 5);
    }

    private function createBodyBoldTextStyle(Document $document): TextStyle
    {
        $font = $document->getOrCreateDefaultFont(Document\Resource\Font\DefaultFont::FONT_TIMES, Document\Resource\Font\DefaultFont::STYLE_BOLD);

        return new TextStyle($font, 5);
    }

    private function createHeaderTextStyle(Document $document): TextStyle
    {
        $font = $document->getOrCreateDefaultFont(Document\Resource\Font\DefaultFont::FONT_HELVETICA, Document\Resource\Font\DefaultFont::STYLE_DEFAULT);

        return new TextStyle($font, 8);
    }

    private function render(Document $document): string
    {
        $catalog = $document->render()->render();
        $fonts = $catalog->getPages()->getPages()[0]->getResources()->getFonts();
        for ($i = 0; $i < \count($fonts); ++$i) {
            $font = $fonts[$i];
            if ($font instanceof Type0) {
                $type0Font = $fonts[$i]->getDescendantFont()->getFontDescriptor()->getFontFile3()->getFontData();
                file_put_contents('subset'.$i.'.ttf', $type0Font);
            }
        }

        $result = $catalog->save();
        file_put_contents('pdf.pdf', $result);

        return $result;
    }

    private function createRectangleStyle(): RectangleStyle
    {
        return new RectangleStyle(0.5, new Color(200, 120, 0), new Color(220, 100, 0));
    }
}
