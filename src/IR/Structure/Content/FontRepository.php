<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Content;

use PdfGenerator\Backend\Document;
use PdfGenerator\Backend\Structure\Font;
use PdfGenerator\Backend\Structure\Font\Type0;
use PdfGenerator\Backend\Structure\Font\Type1;
use PdfGenerator\Font\Backend\FileWriter;
use PdfGenerator\Font\IR\Optimizer;
use PdfGenerator\Font\IR\Parser;
use PdfGenerator\IR\Structure\Content\Font\Type0Container;

class FontRepository
{
    /**
     * @var Document
     */
    private $document;

    /**
     * @var Font
     */
    private $activeFont;

    /**
     * @var Type0Container[]
     */
    private $type0ContainerCache = [];

    /**
     * @var Type1[]
     */
    private $type1FontCache = [];

    /**
     * @var string[][]
     */
    private $defaultFonts = [
        self::FONT_HELVETICA => [
            self::STYLE_DEFAULT => Type1::BASE_FONT_HELVETICA,
            self::STYLE_OBLIQUE => Type1::BASE_FONT_HELVETICA__OBLIQUE,
            self::STYLE_BOLD => Type1::BASE_FONT_HELVETICA__BOLD,
            self::STYLE_BOLD_OBLIQUE => Type1::BASE_FONT_HELVETICA__BOLDOBLIQUE,
        ],
        self::FONT_COURIER => [
            self::STYLE_ROMAN => Type1::BASE_FONT_COURIER,
            self::STYLE_OBLIQUE => Type1::BASE_FONT_COURIER__OBLIQUE,
            self::STYLE_BOLD => Type1::BASE_FONT_COURIER__BOLD,
            self::STYLE_BOLD_OBLIQUE => Type1::BASE_FONT_COURIER__BOLDOBLIQUE,
        ],
        self::FONT_TIMES => [
            self::STYLE_ROMAN => Type1::BASE_FONT_TIMES__ROMAN,
            self::STYLE_ITALIC => Type1::BASE_FONT_TIMES__ITALIC,
            self::STYLE_BOLD => Type1::BASE_FONT_TIMES__BOLD,
            self::STYLE_BOLD_ITALIC => Type1::BASE_FONT_TIMES__BOLDITALIC,
        ],
        self::FONT_ZAPFDINGBATS => [
            self::STYLE_DEFAULT => Type1::BASE_FONT_ZAPFDINGBATS,
        ],
        self::FONT_SYMBOL => [
            self::STYLE_DEFAULT => Type1::BASE_FONT_SYMBOL,
        ],
    ];

    const FONT_HELVETICA = 'Helvetica';
    const FONT_COURIER = 'Courier';
    const FONT_TIMES = 'Times';
    const FONT_SYMBOL = 'Symbol';
    const FONT_ZAPFDINGBATS = 'ZapfDingbats';

    const STYLE_DEFAULT = self::STYLE_ROMAN;
    const STYLE_ROMAN = 'ROMAN';
    const STYLE_ITALIC = 'ITALIC';
    const STYLE_BOLD = 'BOLD';
    const STYLE_OBLIQUE = 'OBLIQUE';
    const STYLE_BOLD_OBLIQUE = 'BOLD_OBLIQUE';
    const STYLE_BOLD_ITALIC = 'BOLD_ITALIC';

    /**
     * FontRepository constructor.
     *
     * @param Document $document
     */
    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    /**
     * @throws \Exception
     *
     * @return Font
     */
    public function getActiveFont()
    {
        if ($this->activeFont === null) {
            $this->activeFont = $this->getDefaultFont(self::FONT_HELVETICA, self::STYLE_DEFAULT);
        }

        return $this->activeFont;
    }

    /**
     * @param Font $font
     */
    public function setActiveFont(Font $font)
    {
        $this->activeFont = $font;
    }

    /**
     * @param string $font
     * @param string $style
     *
     * @throws \Exception
     *
     * @return Type1
     */
    public function getDefaultFont(string $font, string $style)
    {
        if (!\array_key_exists($font, $this->defaultFonts)) {
            throw new \Exception('The font ' . $font . ' is not part of the default set.');
        }

        if (!\array_key_exists($style, $this->defaultFonts[$font])) {
            throw new \Exception('This font style ' . $style . ' is not part of the default set.');
        }

        $fontName = $this->defaultFonts[$font][$style];
        $type1Font = $this->document->getResourcesBuilder()->getResources()->addType1Font($fontName);

        $this->type1FontCache[$type1Font->getIdentifier()] = $type1Font;

        return $type1Font;
    }

    /**
     * @param string $path
     *
     * @throws \Exception
     *
     * @return Type0
     */
    public function getFont(string $path)
    {
        $container = $this->getType0Container($path);
        $this->type0ContainerCache[$container->getType0Font()->getIdentifier()] = $container;

        return $container->getType0Font();
    }

    /**
     * @param string $path
     *
     * @throws \Exception
     *
     * @return Type0Container
     */
    private function getType0Container(string $path)
    {
        $container = new Type0Container();

        $type0Font = $this->document->getResourcesBuilder()->getResources()->addType0Font();
        $container->setType0Font($type0Font);

        $parser = Parser::create();
        $fontContent = file_get_contents($path);
        $font = $parser->parse($fontContent);
        $container->setFont($font);

        return $container;
    }

    /**
     * fills the font content with the.
     *
     * @throws \Exception
     */
    public function finalizeFonts()
    {
        foreach ($this->type0ContainerCache as $type0Container) {
            $optimizer = new Optimizer();
            $fontSubset = $optimizer->getFontSubset($type0Container->getFont(), $type0Container->getMappedCharacters());

            $writer = FileWriter::create();
            $content = $writer->writeFont($fontSubset);

            $cIDSystemInfo = new Font\Structure\CIDSystemInfo();
            $cIDSystemInfo->setRegistry('famoser');
            $cIDSystemInfo->setOrdering(1);
            $cIDSystemInfo->setSupplement(1);

            $cidFont = new Font\Structure\CIDFont();
            $cidFont->setSubType(Font\Structure\CIDFont::SUBTYPE_CID_FONT_TYPE_2);
            $cidFont->setDW(1000);
            $cidFont->setCIDSystemInfo($cIDSystemInfo);
            $cidFont->setFontDescriptor();
            $cidFont->setBaseFont();

            $widths = [];
            $cidFont->setW();

            $type0Font = $type0Container->getType0Font();
            $type0Font->setDescendantFont();
        }
    }

    /**
     * @param string $text
     * @param Font $font
     *
     * @throws \Exception
     *
     * @return string[]
     */
    public function mapText(string $text, Font $font)
    {
        // split by newlines
        $cleanedText = str_replace("\n\r", "\n", $text);
        $lines = explode("\n", $cleanedText);

        $fontIdentifier = $font->getIdentifier();
        if (\array_key_exists($fontIdentifier, $this->type1FontCache)) {
            // type 1 fonts use a standard encoding.
            // we just assume the user knows this and the input text is as expected
            // TODO: convert it to the default encoding used by the standard fonts
            return $lines;
        }

        if (\array_key_exists($fontIdentifier, $this->type0ContainerCache)) {
            $container = $this->type0ContainerCache[$fontIdentifier];

            $result = [];
            foreach ($lines as $line) {
                $result[] = $this->mapType0Text($line, $container);
            }

            return $result;
        }

        throw new \Exception('unknown font with identifier ' . $font->getIdentifier());
    }

    /**
     * @param string $text
     * @param Type0Container $container
     *
     * @return string
     */
    private function mapType0Text(string $text, Type0Container $container)
    {
        $mapped = '';

        $length = mb_strlen($text);
        for ($i = 0; $i < $length; ++$i) {
            $char = mb_substr($text, $i, 1);

            $mapped .= $container->getOrCreateMapping($char);
        }

        return $mapped;
    }
}
