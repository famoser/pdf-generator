<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure;

use PdfGenerator\Backend\Catalog\Font\Structure\CIDFont;
use PdfGenerator\Backend\Catalog\Font\Structure\CIDSystemInfo;
use PdfGenerator\Backend\Catalog\Font\Structure\FontDescriptor;
use PdfGenerator\Backend\Catalog\Font\Structure\FontStream;
use PdfGenerator\Backend\Catalog\Font\TrueType;
use PdfGenerator\Backend\Catalog\Font\Type0;
use PdfGenerator\Backend\Catalog\Font\Type1;
use PdfGenerator\Backend\Catalog\Image as CatalogImage;
use PdfGenerator\Backend\Structure\Document\Font\CMapCreator;
use PdfGenerator\Backend\Structure\Document\Font\DefaultFont;
use PdfGenerator\Backend\Structure\Document\Font\EmbeddedFont;
use PdfGenerator\Backend\Structure\Document\Image;
use PdfGenerator\Backend\Structure\Optimization\Configuration;
use PdfGenerator\Backend\Structure\Optimization\FontOptimizer;
use PdfGenerator\Backend\Structure\Optimization\ImageOptimizer;
use PdfGenerator\Font\Frontend\File\Table\HHeaTable;
use PdfGenerator\Font\Frontend\File\Table\OS2Table;
use PdfGenerator\Font\IR\Structure\Character;
use PdfGenerator\Font\IR\Structure\Font;

class DocumentVisitor
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var int[]
     */
    private $resourceCounters = [];

    /**
     * @var FontOptimizer
     */
    private $fontOptimizer;

    /**
     * @var CMapCreator
     */
    private $cMapCreator;

    /**
     * @var ImageOptimizer
     */
    private $imageOptimizer;

    /**
     * DocumentVisitor constructor.
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;

        $this->fontOptimizer = new FontOptimizer();
        $this->cMapCreator = new CMapCreator();
        $this->imageOptimizer = new ImageOptimizer();
    }

    /**
     * @return string
     */
    private function generateIdentifier(string $prefix)
    {
        if (!\array_key_exists($prefix, $this->resourceCounters)) {
            $this->resourceCounters[$prefix] = 0;

            return $prefix;
        }

        return $prefix . (string)($this->resourceCounters[$prefix]++);
    }

    /**
     * @return CatalogImage
     */
    public function visitImage(Image $param)
    {
        $identifier = $this->generateIdentifier('I');
        $type = $param->getType() === Image::TYPE_JPG || $param->getType() === Image::TYPE_JPEG ? CatalogImage::IMAGE_TYPE_JPEG : null;

        $content = $param->getImageContent();
        $width = $param->getWidth();
        $height = $param->getHeight();

        if ($this->configuration->getAutoResizeImages()) {
            list($targetWidth, $targetHeight) = $this->imageOptimizer->getTargetHeightWidth($width, $height, $param->getMaxUsedWidth(), $param->getMaxUsedHeight(), $this->configuration->getAutoResizeImagesDpi());

            if ($targetWidth < $width) {
                $width = (int)$targetWidth;
                $height = (int)$targetHeight;

                $content = $this->imageOptimizer->transformToJpgAndResize($content, $width, $height);
                $type = CatalogImage::IMAGE_TYPE_JPEG;
            }
        }

        if ($type === null) {
            $content = $this->imageOptimizer->transformToJpgAndResize($content, $width, $height);
            $type = CatalogImage::IMAGE_TYPE_JPEG;
        }

        return new CatalogImage($identifier, $type, $content, $width, $height);
    }

    /**
     * @return Type1
     */
    public function visitDefaultFont(DefaultFont $param)
    {
        $identifier = $this->generateIdentifier('F');

        return new Type1($identifier, $param->getBaseFont());
    }

    /**
     * @throws \Exception
     *
     * @return Type0|TrueType
     */
    public function visitEmbeddedFont(EmbeddedFont $param)
    {
        $font = $param->getFont();
        $fontData = $param->getFontData();

        $createFontSubsets = $this->configuration->getCreateFontSubsets();
        if ($createFontSubsets) {
            list($font, $fontData, $usedCodepoints) = $this->fontOptimizer->createFontSubset($font, $param->getCharactersUsedInText());
        }

        $fontName = $font->getFontInformation()->getFullName() ?? 'invalidFontName';
        $fontName = strtr($fontName, [' ' => '']); // remove any spaces in name

        $fontStream = new FontStream();
        $fontStream->setFontData($fontData);
        $fontStream->setSubtype(FontStream::SUBTYPE_OPEN_TYPE);

        // glyph space -> text space (=em) is in units of 1000 for PDF
        // the font defines its own sizing in the head table, hence we need to normalize the units
        $sizeNormalizer = 1000 / $font->getTableDirectory()->getHeadTable()->getUnitsPerEm();
        $fontDescriptor = $this->getFontDescriptor($fontName, $font, $fontStream, $sizeNormalizer);

        if ($createFontSubsets && !$this->configuration->getUseTTFFonts()) {
            return $this->createType0Font($fontDescriptor, $font, $sizeNormalizer, $usedCodepoints);
        }

        return $this->createTrueTypeFont($fontDescriptor, $font->getCharacters(), $sizeNormalizer);
    }

    /**
     * @param Character[] $characters
     */
    private function createTrueTypeFont(FontDescriptor $fontDescriptor, array $characters, float $sizeNormalizer): TrueType
    {
        $widths = [];

        // default value is 0
        for ($i = 0; $i < 255; ++$i) {
            $widths[$i] = 0;
        }

        // add widths of windows code page
        foreach ($characters as $character) {
            // create windows character set
            $mappingIndex = $character->getUnicodePoint() ? $this->getWindows1252Mapping($character->getUnicodePoint()) : null;
            if ($mappingIndex !== null) {
                $widths[$mappingIndex] = (int)($character->getLongHorMetric()->getAdvanceWidth() * $sizeNormalizer);
            }
        }

        $identifier = $this->generateIdentifier('F');

        return new TrueType($identifier, $fontDescriptor, $widths);
    }

    /**
     * @return Type0
     */
    private function createType0Font(FontDescriptor $fontDescriptor, Font $font, float $sizeNormalizer, array $usedCodepoints)
    {
        /** @var int[] $characterWidths */
        $characterWidths = [];
        $characters = array_merge($font->getReservedCharacters(), $font->getCharacters());
        foreach ($characters as $character) {
            $characterWidths[] = (int)($character->getLongHorMetric()->getAdvanceWidth() * $sizeNormalizer);
        }

        // start at CID 0 with our widths
        $widths = [0 => $characterWidths];

        $cIDSystemInfo = new CIDSystemInfo();
        $cIDSystemInfo->setRegistry('famoser');
        $cIDSystemInfo->setOrdering('custom-1');
        $cIDSystemInfo->setSupplement(1);

        $cidFont = new CIDFont();
        $cidFont->setSubType(CIDFont::SUBTYPE_CID_FONT_TYPE_2);
        $cidFont->setDW(500);
        $cidFont->setCIDSystemInfo($cIDSystemInfo);
        $cidFont->setFontDescriptor($fontDescriptor);
        $cidFont->setBaseFont($fontDescriptor->getFontName());
        $cidFont->setW($widths);

        $identifier = $this->generateIdentifier('F');
        $type0Font = new Type0($identifier);
        $type0Font->setDescendantFont($cidFont);
        $type0Font->setBaseFont($fontDescriptor->getFontName());

        $cMapName = $fontDescriptor->getFontName() . 'CMap';
        $characterIndexCMap = $this->cMapCreator->createTextToCharacterIndexCMap($cIDSystemInfo, $cMapName, $characters, $usedCodepoints);
        $type0Font->setEncoding($characterIndexCMap);

        $cMapInvertedName = $fontDescriptor->getFontName() . 'CMapInverted';
        $unicodeCMap = $this->cMapCreator->createCharacterIndexToUnicodeCMap($cIDSystemInfo, $cMapInvertedName, $characters);
        $type0Font->setToUnicode($unicodeCMap);

        return $type0Font;
    }

    private function getFontDescriptor(string $fontName, Font $font, FontStream $fontStream, float $sizeNormalizer): FontDescriptor
    {
        $HHeaTable = $font->getTableDirectory()->getHHeaTable();
        $OS2Table = $font->getTableDirectory()->getOS2Table();

        $fontDescriptor = new FontDescriptor();
        $fontDescriptor->setFontName($fontName);

        $BBox = $this->getFontBBox($font->getCharacters(), $sizeNormalizer);
        $fontDescriptor->setFontBBox($BBox);

        $angle = $this->getFontItalicAngle($HHeaTable);

        $fontFlags = $this->calculateFontFlags($OS2Table, $angle > 0);
        $fontDescriptor->setFlags($fontFlags);

        $fontDescriptor->setItalicAngle((int)$angle);
        $fontDescriptor->setAscent($HHeaTable->getAscent() * $sizeNormalizer);
        $fontDescriptor->setDescent($HHeaTable->getDescent() * $sizeNormalizer);
        $fontDescriptor->setCapHeight((int)($OS2Table->getSCapHeight() * $sizeNormalizer));
        $fontDescriptor->setStemV(0); // TODO find out where to get this from
        $fontDescriptor->setFontFile3($fontStream);

        return $fontDescriptor;
    }

    /**
     * @param Character[] $characters
     *
     * @return int[]
     */
    private function getFontBBox(array $characters, float $sizeNormalizer): array
    {
        $xMin = 0;
        $xMax = 0;
        $yMin = 0;
        $yMax = 0;
        foreach ($characters as $character) {
            if ($character->getGlyfTable() === null) {
                continue;
            }

            $xMin = max($xMin, $character->getGlyfTable()->getXMin());
            $xMax = max($xMax, $character->getGlyfTable()->getXMax());
            $yMin = max($yMin, $character->getGlyfTable()->getYMin());
            $yMax = max($yMax, $character->getGlyfTable()->getYMax());
        }

        return [(int)($xMin * $sizeNormalizer), ((int)($yMin * $sizeNormalizer)), ((int)($xMax * $sizeNormalizer)), (int)($yMax * $sizeNormalizer)];
    }

    private function getFontItalicAngle(HHeaTable $HHeaTable): float
    {
        if ($HHeaTable->getCaretSlopeRun() === 0) {
            return 0;
        }

        return tanh($HHeaTable->getCaretSlopeRise() / $HHeaTable->getCaretSlopeRun()) - 90;
    }

    private function calculateFontFlags(OS2Table $OS2Table, bool $isItalic): int
    {
        $flags = 0;

        $panose = $OS2Table->getPanose();

        // fixed pitch
        if ($panose[3] === 9) { // when proportion is monospaced
            $flags = $flags | FontDescriptor::FLAG_FIXED_PITCH;
        }

        // serif
        if ($panose[1] >= 11 && $panose[1] <= 13) { // when serif style is normal sans, obtuse sans or perpendicular sans
            $flags = $flags | FontDescriptor::FLAG_SERIF;
        }

        // always symbolic (characters outside adobe standard set)
        $flags = $flags | FontDescriptor::FLAG_SYMBOLIC;

        // script (cursive)
        if ($panose[0] === 3) { // when family type is hand-written
            $flags = $flags | FontDescriptor::FLAG_SCRIPT;
        }

        // italic
        if ($isItalic) {
            $flags = $flags | FontDescriptor::FLAG_ITALIC;
        }

        return $flags;
    }

    /**
     * @param int $getUnicodePoint
     */
    private function getWindows1252Mapping(int $unicodePoint): ?int
    {
        if ($unicodePoint < 0x80) {
            return $unicodePoint;
        }

        if ($unicodePoint >= 0xA0 && $unicodePoint <= 0xFF) {
            return $unicodePoint;
        }

        switch ($unicodePoint) {
            case 0x20AC:
                return 0x80;
            case 0x201A:
                return 0x82;
            case 0x0192:
                return 0x83;
            case 0x201E:
                return 0x84;
            case 0x2026:
                return 0x85;
            case 0x2020:
                return 0x86;
            case 0x2021:
                return 0x87;
            case 0x20C6:
                return 0x88;
            case 0x2030:
                return 0x89;
            case 0x0160:
                return 0x8A;
            case 0x2039:
                return 0x8B;
            case 0x0152:
                return 0x8C;
            case 0x017D:
                return 0x8E;

            case 0x2018:
                return 0x91;
            case 0x2019:
                return 0x92;
            case 0x201C:
                return 0x93;
            case 0x201D:
                return 0x94;
            case 0x2022:
                return 0x95;
            case 0x2013:
                return 0x96;
            case 0x2014:
                return 0x97;
            case 0x02DC:
                return 0x98;
            case 0x2122:
                return 0x99;
            case 0x0161:
                return 0x9A;
            case 0x203A:
                return 0x9B;
            case 0x0153:
                return 0x9C;
            case 0x017E:
                return 0x9E;
            case 0x0178:
                return 0x9F;
        }

        return null;
    }
}
