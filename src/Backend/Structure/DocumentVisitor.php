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
use PdfGenerator\Font\Backend\FileWriter;
use PdfGenerator\Font\IR\Optimizer;
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

        return new Type1($identifier, $param->getBaseFont(), $param->getEncoding());
    }

    /**
     * @throws \Exception
     *
     * @return Type0
     */
    public function visitEmbeddedFont(EmbeddedFont $param)
    {
        $font = $param->getFont();

        $fontSubsetDefinition = $this->fontOptimizer->generateFontSubset($font, $param->getUsedWithText());

        // create subset
        $optimizer = Optimizer::create();
        $fontSubset = $optimizer->getFontSubset($font, $fontSubsetDefinition->getCharacters());

        $writer = FileWriter::create();
        $content = $writer->writeFont($fontSubset);

        $characterWidths = [];
        $sizeNormalizer = 1024 / $font->getTableDirectory()->getHeadTable()->getUnitsPerEm(); // this could be a hack; but else the pdf character widths look messed up
        foreach ($fontSubset->getCharacters() as $character) {
            $characterWidths[] = (int)($character->getLongHorMetric()->getAdvanceWidth() * $sizeNormalizer);
        }
        $widths[0] = array_merge([$characterWidths[0]], $characterWidths); // the initial character is mapped twice; to .notdef and U+0000. need to have both widths therefore

        $fontName = $font->getFontInformation()->getFullName() ?? 'invalidFontName';

        $fontStream = new FontStream();
        $fontStream->setFontData($content);
        $fontStream->setSubtype(FontStream::SUBTYPE_OPEN_TYPE);

        $cIDSystemInfo = new CIDSystemInfo();
        $cIDSystemInfo->setRegistry('famoser');
        $cIDSystemInfo->setOrdering('custom-1');
        $cIDSystemInfo->setSupplement(1);

        $fontDescriptor = $this->getFontDescriptor($fontName, $font, $fontStream, $sizeNormalizer);

        $cidFont = new CIDFont();
        $cidFont->setSubType(CIDFont::SUBTYPE_CID_FONT_TYPE_2);
        $cidFont->setDW(500);
        $cidFont->setCIDSystemInfo($cIDSystemInfo);
        $cidFont->setFontDescriptor($fontDescriptor);
        $cidFont->setBaseFont($fontName);
        $cidFont->setW($widths);

        $identifier = $this->generateIdentifier('F');
        $type0Font = new Type0($identifier);
        $type0Font->setDescendantFont($cidFont);
        $type0Font->setBaseFont($fontName);

        $characterIndexCMap = $this->cMapCreator->createTextToCharacterIndexCMap($cIDSystemInfo, 'someName', $fontSubsetDefinition);
        $type0Font->setEncoding($characterIndexCMap);

        $unicodeCMap = $this->cMapCreator->createCharacterIndexToUnicodeCMap($cIDSystemInfo, 'someNameInverted', $fontSubsetDefinition);
        $type0Font->setToUnicode($unicodeCMap);

        return $type0Font;
    }

    private function getFontDescriptor(string $fontName, Font $font, FontStream $fontStream, $sizeNormalizer): FontDescriptor
    {
        $HHeaTable = $font->getTableDirectory()->getHHeaTable();
        $OS2Table = $font->getTableDirectory()->getOS2Table();

        $fontDescriptor = new FontDescriptor();
        $fontDescriptor->setFontName($fontName);
        $fontDescriptor->setFlags(0); // could calculate from OS/2 IBM font family

        $xMin = 0;
        $xMax = 0;
        $yMin = 0;
        $yMax = 0;
        foreach ($font->getCharacters() as $character) {
            if ($character->getGlyfTable() === null) {
                continue;
            }

            $xMin = max($xMin, $character->getGlyfTable()->getXMin());
            $xMax = max($xMax, $character->getGlyfTable()->getXMax());
            $yMin = max($yMin, $character->getGlyfTable()->getYMin());
            $yMax = max($yMax, $character->getGlyfTable()->getYMax());
        }

        $fontDescriptor->setFontBBox([(int)($xMin * $sizeNormalizer), ((int)($yMin * $sizeNormalizer)), ((int)($xMax * $sizeNormalizer)), (int)($yMax * $sizeNormalizer)]);

        if ($HHeaTable->getCaretSlopeRun() !== 0) {
            $angle = tanh($HHeaTable->getCaretSlopeRise() / $HHeaTable->getCaretSlopeRun()) - 90;
        } else {
            $angle = 0;
        }

        $fontDescriptor->setItalicAngle($angle);
        $fontDescriptor->setAscent($HHeaTable->getAscent() * $sizeNormalizer);
        $fontDescriptor->setDecent($HHeaTable->getDecent() * $sizeNormalizer);
        $fontDescriptor->setCapHeight((int)($OS2Table->getSCapHeight() * $sizeNormalizer));
        $fontDescriptor->setStemV(0); // TODO find out where to get this from
        $fontDescriptor->setFontFile3($fontStream);

        return $fontDescriptor;
    }
}
