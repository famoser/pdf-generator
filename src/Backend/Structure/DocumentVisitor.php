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
                $content = $this->imageOptimizer->transformToJpgAndResize($content, $targetWidth, $targetHeight);
                $width = $targetWidth;
                $height = $targetHeight;
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

        $widths = [];
        foreach ($fontSubset->getCharacters() as $character) {
            $widths[] = $character->getLongHorMetric()->getAdvanceWidth();
        }

        $fontName = $font->getFontInformation()->getFullName();

        $fontStream = new FontStream();
        $fontStream->setFontData($content);
        $fontStream->setSubtype(FontStream::SUBTYPE_OPEN_TYPE);

        $cIDSystemInfo = new CIDSystemInfo();
        $cIDSystemInfo->setRegistry('famoser');
        $cIDSystemInfo->setOrdering('custom-1');
        $cIDSystemInfo->setSupplement(1);

        $fontDescriptor = $this->getFontDescriptor($fontName, $font, $fontStream);

        $cidFont = new CIDFont();
        $cidFont->setSubType(CIDFont::SUBTYPE_CID_FONT_TYPE_2);
        $cidFont->setDW(1000);
        $cidFont->setCIDSystemInfo($cIDSystemInfo);
        $cidFont->setFontDescriptor($fontDescriptor);
        $cidFont->setBaseFont($fontName);
        $cidFont->setW($widths);

        $identifier = $this->generateIdentifier('F');
        $type0Font = new Type0($identifier);
        $type0Font->setDescendantFont($cidFont);
        $type0Font->setBaseFont($fontName);

        $cMap = $this->cMapCreator->createCMap($cIDSystemInfo, 'someName', $fontSubsetDefinition->getCodePoints(), $fontSubsetDefinition->getMissingCodePoints());
        $type0Font->setEncoding($cMap);
        $type0Font->setToUnicode($cMap); // TODO: unicode CMap not implemented yet

        return $type0Font;
    }

    private function getFontDescriptor(string $fontName, Font $font, FontStream $fontStream): FontDescriptor
    {
        $fontDescriptor = new FontDescriptor();
        $fontDescriptor->setFontName($fontName);
        $HHeaTable = $font->getTableDirectory()->getHHeaTable();
        $fontDescriptor->setFlags(0); // TODO calculate from
        $fontDescriptor->setFontBBox([0, 0]); // TODO calculate from characters
        $fontDescriptor->setItalicAngle(0); // TODO  get from postscript table
        $fontDescriptor->setAscent($HHeaTable->getAscent());
        $fontDescriptor->setDecent($HHeaTable->getDecent());
        $fontDescriptor->setCapHeight(0); // TODO get from OS/2 table
        $fontDescriptor->setStemV(0); // TODO find out where to get this from
        $fontDescriptor->setFontFile3($fontStream);

        return $fontDescriptor;
    }
}
