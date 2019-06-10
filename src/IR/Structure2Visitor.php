<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR;

use PdfGenerator\Backend\Structure\Catalog;
use PdfGenerator\Backend\Structure\Font\Structure\CIDFont;
use PdfGenerator\Backend\Structure\Font\Structure\CIDSystemInfo;
use PdfGenerator\Backend\Structure\Font\Structure\FontDescriptor;
use PdfGenerator\Backend\Structure\Font\Structure\FontStream;
use PdfGenerator\Backend\Structure\Font\Type0;
use PdfGenerator\Backend\Structure\Font\Type1;
use PdfGenerator\Font\Backend\FileWriter;
use PdfGenerator\Font\IR\Optimizer;
use PdfGenerator\Font\IR\Parser;
use PdfGenerator\IR\Structure2\Font\DefaultFont;
use PdfGenerator\IR\Transformation\Document\Font\DefaultFontMapping;

class Structure2Visitor
{
    /**
     * @var int[]
     */
    private $resourceCounters = [];

    /**
     * @param string $prefix
     *
     * @return string
     */
    private function generateIdentifier(string $prefix)
    {
        if (!\array_key_exists($prefix, $this->resourceCounters)) {
            $this->resourceCounters[$prefix] = 0;
        }

        return $prefix . $this->resourceCounters[$prefix]++;
    }

    public static function renderDocument(Structure2\Document $param)
    {
        $catalog = new Catalog();
    }

    /**
     * @param DefaultFont $param
     *
     * @throws \Exception
     *
     * @return Type1
     */
    public function visitDefaultFont(DefaultFont $param)
    {
        $identifier = $this->generateIdentifier('F');
        $baseFont = $this->getDefaultFontBaseFont($param->getFont(), $param->getStyle());

        return new Type1($identifier, $baseFont);
    }

    /**
     * @param string $font
     * @param string $style
     *
     * @throws \Exception
     *
     * @return string
     */
    private function getDefaultFontBaseFont(string $font, string $style): string
    {
        if (!\array_key_exists($font, DefaultFontMapping::$defaultFontMapping)) {
            throw new \Exception('The font ' . $font . ' is not part of the default set.');
        }

        $styles = DefaultFontMapping::$defaultFontMapping[$font];
        if (!\array_key_exists($style, $styles)) {
            throw new \Exception('This font style ' . $style . ' is not part of the default set.');
        }

        return $styles[$style];
    }

    /**
     * @param Structure2\Font\EmbeddedFont $param
     *
     * @throws \Exception
     *
     * @return Type0
     */
    public function visitEmbeddedFont(Structure2\Font\EmbeddedFont $param)
    {
        $parser = Parser::create();
        $fontContent = file_get_contents($param->getFontPath());
        $font = $parser->parse($fontContent);

        $optimizer = Optimizer::create();
        $fontSubset = $optimizer->getFontSubset($font, $characters);

        $writer = FileWriter::create();
        $fontContent = $writer->writeFont($fontSubset);

        $baseFontName = 'Some';

        $fontStream = new FontStream();
        $fontStream->setFontData($fontContent);
        $fontStream->setSubtype(FontStream::SUBTYPE_OPEN_TYPE);

        $cIDSystemInfo = new CIDSystemInfo();
        $cIDSystemInfo->setRegistry('famoser');
        $cIDSystemInfo->setOrdering(1);
        $cIDSystemInfo->setSupplement(1);

        $fontDescriptor = new FontDescriptor();
        // TODO: missing properties
        $fontDescriptor->setFontFile3($fontStream);

        $cidFont = new CIDFont();
        $cidFont->setSubType(CIDFont::SUBTYPE_CID_FONT_TYPE_2);
        $cidFont->setDW(1000);
        $cidFont->setCIDSystemInfo($cIDSystemInfo);
        $cidFont->setFontDescriptor($fontDescriptor);
        $cidFont->setBaseFont($baseFontName);

        $characterWidth = [$fontSubset->getMissingGlyphCharacter()->getLongHorMetric()->getAdvanceWidth()];
        foreach ($fontSubset->getCharacters() as $character) {
            $characterWidth[] = $character->getLongHorMetric()->getAdvanceWidth();
        }

        $cidFont->setW($characterWidth);

        $identifier = $this->generateIdentifier('F');
        $type0Font = new Type0($identifier);
        $type0Font->setDescendantFont($cidFont);
        $type0Font->setBaseFont($baseFontName);
        $type0Font->setEncoding($cMap);
        $type0Font->setToUnicode($cMap);

        return $type0Font;
    }
}
