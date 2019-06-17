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
use PdfGenerator\Backend\Catalog\Image;
use PdfGenerator\Backend\Structure\Document\Font\CMapCreator;
use PdfGenerator\Backend\Structure\Document\Font\DefaultFont;
use PdfGenerator\Backend\Structure\Document\Font\EmbeddedFont;
use PdfGenerator\Font\Backend\FileWriter;
use PdfGenerator\IR\Structure\Optimization\Configuration;
use PdfGenerator\IR\Structure\Optimization\FontOptimizer;
use PdfGenerator\IR\Structure\Optimization\ImageOptimizer;

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
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;

        $this->fontOptimizer = new FontOptimizer();
        $this->cMapCreator = new CMapCreator();
        $this->imageOptimizer = new ImageOptimizer();
    }

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

    /**
     * @param Document\Image $param
     *
     * @return Image
     */
    public function visitImage(Document\Image $param)
    {
        $identifier = $this->generateIdentifier('I');
        $type = $param->getType() === Document\Image::TYPE_JPG || $param->getType() === Document\Image::TYPE_JPEG ? Image::IMAGE_TYPE_JPEG : null;

        $content = $param->getImageContent();
        list($width, $height) = getimagesizefromstring($param->getImageContent());

        if ($this->configuration->getAutoResizeImages()) {
            list($targetWidth, $targetHeight) = $this->imageOptimizer->getTargetHeightWidth($width, $height, $param->getMaxUsedWidth(), $param->getMaxUsedHeight(), $this->configuration->getAutoResizeImagesDpi());

            if ($targetWidth < $width) {
                $content = $this->imageOptimizer->transformToJpgAndResize($content, $targetWidth, $targetHeight);
                $width = $targetWidth;
                $height = $targetHeight;
                $type = Image::IMAGE_TYPE_JPEG;
            }
        }

        if ($type === null) {
            $content = $this->imageOptimizer->transformToJpgAndResize($content, $width, $height);
            $type = Image::IMAGE_TYPE_JPEG;
        }

        return new Image($identifier, $type, $content, $width, $height);
    }

    /**
     * @param DefaultFont $param
     *
     * @return Type1
     */
    public function visitDefaultFont(DefaultFont $param)
    {
        $identifier = $this->generateIdentifier('F');

        return new Type1($identifier, $param->getBaseFont(), $param->getEncoding());
    }

    /**
     * @param EmbeddedFont $param
     *
     * @throws \Exception
     *
     * @return Type0
     */
    public function visitEmbeddedFont(EmbeddedFont $param)
    {
        $orderedCodepoints = $this->fontOptimizer->getOrderedCodepoints($param->getFont());

        $font = $param->getFont();

        $fontSubset = $this->fontOptimizer->getFontSubset($font, $orderedCodepoints);

        $writer = FileWriter::create();
        $content = $writer->writeFont($fontSubset);

        $widths = [];
        foreach ($fontSubset->getCharacters() as $character) {
            $widths[] = $character->getLongHorMetric()->getAdvanceWidth();
        }

        // TODO: need to parse name table to fix this
        $fontName = 'SomeFont';

        $fontStream = new FontStream();
        $fontStream->setFontData($content);
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
        $cidFont->setBaseFont($fontName);
        $cidFont->setW($widths);

        $identifier = $this->generateIdentifier('F');
        $type0Font = new Type0($identifier);
        $type0Font->setDescendantFont($cidFont);
        $type0Font->setBaseFont($fontName);

        // TODO: CMaps not implemented yet
        $cMap = $this->cMapCreator->createCMap($cIDSystemInfo, 'someName', $orderedCodepoints);
        $type0Font->setEncoding($cMap);
        $type0Font->setToUnicode($cMap);

        return $type0Font;
    }
}
