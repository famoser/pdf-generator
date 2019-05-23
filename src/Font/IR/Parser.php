<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\IR;

use PdfGenerator\Font\Frontend\File\FontFile;
use PdfGenerator\Font\Frontend\File\Table\CMap\FormatReader;
use PdfGenerator\Font\Frontend\File\Table\CMap\Subtable;
use PdfGenerator\Font\Frontend\File\Table\CMapTable;
use PdfGenerator\Font\Frontend\File\Table\HMtx\LongHorMetric;
use PdfGenerator\Font\Frontend\File\Table\HMtxTable;
use PdfGenerator\Font\Frontend\File\Traits\BoundingBoxTrait;
use PdfGenerator\Font\Frontend\FileReader;
use PdfGenerator\Font\Frontend\StreamReader;
use PdfGenerator\Font\IR\Structure\BoundingBox;
use PdfGenerator\Font\IR\Structure\Character;
use PdfGenerator\Font\IR\Structure\Font;
use PdfGenerator\Font\IR\Structure\PostScriptInfo;
use PdfGenerator\Font\IR\Utils\CMap\GlyphIndexFormatVisitor;
use PdfGenerator\Font\IR\Utils\Post\GlyphInfo;
use PdfGenerator\Font\Resources\GlyphNameMapping\Factory;

class Parser
{
    /**
     * @var GlyphIndexFormatVisitor
     */
    private $cMapGlyphIndexFormatVisitor;

    /**
     * @var \PdfGenerator\Font\IR\Utils\Post\GlyphIndexFormatVisitor
     */
    private $postGlyphIndexFormatVisitor;

    /**
     * @var Factory
     */
    private $glyphNameMappingFactory;

    /**
     * Parser constructor.
     *
     * @param GlyphIndexFormatVisitor $cMapGlyphIndexFormatVisitor
     * @param \PdfGenerator\Font\IR\Utils\Post\GlyphIndexFormatVisitor $postGlyphIndexFormatVisitor
     * @param Factory $glyphNameMappingFactory
     */
    public function __construct(GlyphIndexFormatVisitor $cMapGlyphIndexFormatVisitor, \PdfGenerator\Font\IR\Utils\Post\GlyphIndexFormatVisitor $postGlyphIndexFormatVisitor, Factory $glyphNameMappingFactory)
    {
        $this->cMapGlyphIndexFormatVisitor = $cMapGlyphIndexFormatVisitor;
        $this->postGlyphIndexFormatVisitor = $postGlyphIndexFormatVisitor;
        $this->glyphNameMappingFactory = $glyphNameMappingFactory;
    }

    /**
     * @param string $content
     *
     * @throws \Exception
     *
     * @return Font
     */
    public function parse(string $content): Font
    {
        $streamReader = new StreamReader($content);
        $cMapFormatReader = new FormatReader();
        $postFormatReader = new \PdfGenerator\Font\Frontend\File\Table\Post\FormatReader();
        $fileReader = new FileReader($cMapFormatReader, $postFormatReader);

        $fontFile = $fileReader->readFontFile($streamReader);
        $font = $this->createFont($fontFile);

        return $font;
    }

    /**
     * @param FontFile $fontFile
     *
     * @throws \Exception
     *
     * @return Font
     */
    private function createFont(FontFile $fontFile): Font
    {
        $font = new Font();
        $font->setFontFile($fontFile);

        $characters = $this->createCharacters($fontFile);
        $mappedCharacters = $this->mapCharacters($characters, $fontFile);
        $font->setCharacters($mappedCharacters);

        $missingGlyphCharacter = $characters[0];
        $font->setMissingGlyphCharacter($missingGlyphCharacter);

        return $font;
    }

    /**
     * @param Character[] $characters
     * @param FontFile $fontFile
     *
     * @throws \Exception
     *
     * @return Character[]
     */
    private function mapCharacters(array $characters, FontFile $fontFile)
    {
        $subtable = $this->chooseBestCMapSubtable($fontFile->getCMapTable());

        $cMapMapping = $this->cMapGlyphIndexFormatVisitor->visitFormat($subtable->getFormat());
        $postMapping = $this->postGlyphIndexFormatVisitor->visitFormat($fontFile->getPostTable()->getFormat());
        $aGLFMapping = $this->glyphNameMappingFactory->getAGLFMapping();

        $mappedCharacters = [];
        foreach ($cMapMapping as $unicode => $characterIndex) {
            $character = $characters[$characterIndex];
            if ($character !== null) {
                $character->setUnicodePoint($unicode);

                $glyphInfo = \array_key_exists($characterIndex, $postMapping) ? $postMapping[$characterIndex] : null;
                $aGLFInfo = \array_key_exists($unicode, $aGLFMapping) ? $aGLFMapping[$unicode] : null;
                $postScriptInfo = $this->getPostScriptInfo($glyphInfo, $aGLFInfo);
                $character->setPostScriptInfo($postScriptInfo);

                $mappedCharacters[] = $character;
            }
        }

        return $mappedCharacters;
    }

    /**
     * @param CMapTable $cMapTable
     *
     * @return Subtable
     */
    private function chooseBestCMapSubtable(CMapTable $cMapTable)
    {
        /** @var Subtable[] $cMapSubtable */
        $cMapSubtable = [];

        $overflow = 20;
        foreach ($cMapTable->getSubtables() as $subtable) {
            if ($subtable->getPlatformID() === 0) {
                if ($subtable->getPlatformSpecificID() <= 4) {
                    $cMapSubtable[4 - $subtable->getPlatformSpecificID()] = $subtable;
                    continue;
                }
            }

            if ($subtable->getPlatformID() === 3) {
                $cMapSubtable[10 + 10 - $subtable->getPlatformSpecificID()] = $subtable;
                continue;
            }

            $cMapSubtable[$overflow++] = $subtable;
        }

        $minimalKey = min(array_keys($cMapSubtable));

        return $cMapSubtable[$minimalKey];
    }

    /**
     * @param FontFile $fontFile
     *
     * @return Character[]
     */
    private function createCharacters(FontFile $fontFile): array
    {
        $characters = [];

        $characterCount = \count($fontFile->getGlyfTables());
        for ($i = 0; $i < $characterCount; ++$i) {
            $glyfTable = $fontFile->getGlyfTables()[$i];
            if ($glyfTable === null) {
                $characters[] = null;
                continue;
            }

            $character = new Character();
            $character->setGlyfTable($glyfTable);

            $longHorMetric = $this->getLongHorMetric($fontFile->getHMtxTable(), $i);
            $character->setLongHorMetric($longHorMetric);

            $boundingBox = $this->calculateBoundingBox($glyfTable, $fontFile->getHeadTable()->getUnitsPerEm());
            $character->setBoundingBox($boundingBox);

            $characters[] = $character;
        }

        return $characters;
    }

    /**
     * @param HMtxTable $hMtxTable
     * @param int $entryIndex
     *
     * @return LongHorMetric
     */
    private function getLongHorMetric(HMtxTable $hMtxTable, int $entryIndex): LongHorMetric
    {
        $longHorMetricCount = \count($hMtxTable->getLongHorMetrics());
        if ($entryIndex < $longHorMetricCount) {
            return $hMtxTable->getLongHorMetrics()[$entryIndex];
        }

        $lastEntry = $hMtxTable->getLongHorMetrics()[$longHorMetricCount - 1];
        $bearingIndex = $entryIndex % $longHorMetricCount;
        $bearingEntry = $hMtxTable->getLeftSideBearings()[$bearingIndex];

        $longHorMetric = new LongHorMetric();
        $longHorMetric->setAdvanceWidth($lastEntry->getAdvanceWidth());
        $longHorMetric->setLeftSideBearing($bearingEntry);

        return $longHorMetric;
    }

    /**
     * @param BoundingBoxTrait $boundingBoxTrait
     * @param int $divisor
     *
     * @return BoundingBox
     */
    private function calculateBoundingBox($boundingBoxTrait, int $divisor)
    {
        $boundingBox = new BoundingBox();

        $boundingBox->setHeight((float)($boundingBoxTrait->getYMax() - $boundingBoxTrait->getYMin()) / $divisor);
        $boundingBox->setWidth(((float)$boundingBoxTrait->getXMax() - $boundingBoxTrait->getXMin()) / $divisor);

        return $boundingBox;
    }

    /**
     * @param GlyphInfo|null $glyphInfo
     * @param string|null $aGLFName
     *
     * @return PostScriptInfo
     */
    private function getPostScriptInfo(?GlyphInfo $glyphInfo, ?string $aGLFName)
    {
        $postScriptInfo = new PostScriptInfo();

        if ($glyphInfo === null && $aGLFName === null) {
            $postScriptInfo->setName('.notdef');
            $postScriptInfo->setMacintoshGlyphIndex(0);
        } else {
            if ($glyphInfo === null) {
                $postScriptInfo->setName($aGLFName);
            } else {
                $postScriptInfo->setMacintoshGlyphIndex($glyphInfo->getMacintoshIndex());
                $postScriptInfo->setName($glyphInfo->getName());
            }
        }

        return $postScriptInfo;
    }
}
