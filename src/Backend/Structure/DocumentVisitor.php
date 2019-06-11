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
use PdfGenerator\Backend\Catalog\Font\Structure\CMap;
use PdfGenerator\Backend\Catalog\Font\Structure\FontDescriptor;
use PdfGenerator\Backend\Catalog\Font\Structure\FontStream;
use PdfGenerator\Backend\Catalog\Font\Type0;
use PdfGenerator\Backend\Catalog\Font\Type1;
use PdfGenerator\Backend\Catalog\Image;
use PdfGenerator\Backend\Structure\Document\Font\CharacterMapping;

class DocumentVisitor
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

    /**
     * @param Document\Image $param
     *
     * @return Image
     */
    public function visitImage(Document\Image $param)
    {
        $identifier = $this->generateIdentifier('I');

        return new Image($identifier, $param->getImageType(), $param->getImageContent(), $param->getWidth(), $param->getHeight());
    }

    /**
     * @param Font\DefaultFont $param
     *
     * @return Type1
     */
    public function visitDefaultFont(Font\DefaultFont $param)
    {
        $identifier = $this->generateIdentifier('F');

        return new Type1($identifier, $param->getBaseFont(), $param->getEncoding());
    }

    /**
     * @param Font\EmbeddedFont $param
     *
     * @return Type0
     */
    public function visitEmbeddedFont(Font\EmbeddedFont $param)
    {
        $fontStream = new FontStream();
        $fontStream->setFontData($param->getFontContent());
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
        $cidFont->setBaseFont($param->getBaseFont());
        $cidFont->setW($param->getCharacterWidths());

        $identifier = $this->generateIdentifier('F');
        $type0Font = new Type0($identifier);
        $type0Font->setDescendantFont($cidFont);
        $type0Font->setBaseFont($param->getBaseFont());

        // TODO: CMaps not implemented yet
        $cMap = $this->createCMap($cIDSystemInfo, 'someName', $param->getCharacterMappings());
        $type0Font->setEncoding($cMap);
        $type0Font->setToUnicode($cMap);

        return $type0Font;
    }

    /**
     * @param CIDSystemInfo $cIDSystemInfo
     * @param string $cMapName
     * @param CharacterMapping[] $characterMapping
     *
     * @return CMap
     */
    private function createCMap(CIDSystemInfo $cIDSystemInfo, string $cMapName, array $characterMapping)
    {
        $cmap = new CMap();
        $cmap->setCIDSystemInfo($cIDSystemInfo);
        $cmap->setCMapName($cMapName);

        $header = $this->getCMapHeader($cIDSystemInfo, $cMapName);
        $codeSpaces = $this->getCMapCodeSpaces($characterMapping);

        $cMapData = $header . "\n" . $codeSpaces;
        $cmap->setCMapData($cMapData);

        return $cmap;
    }

    /**
     * @param CIDSystemInfo $cIDSystemInfo
     * @param string $cMapName
     *
     * @return string
     */
    private function getCMapHeader(CIDSystemInfo $cIDSystemInfo, string $cMapName): string
    {
        $commentLines = [];
        $commentLines[] = '%!PS-Adobe-3.0 Resource-CMap';
        $commentLines[] = '%%DocumentNeededResources: procset CIDInit';
        $commentLines[] = '%%IncludeResource: procset CIDInit';
        $commentLines[] = '%%BeginResource: CMap ' . $cMapName;
        $commentLines[] = '%%Title: (' . $cMapName . ' ' . $cIDSystemInfo->getRegistry() . ' ' . $cIDSystemInfo->getOrdering() . ' ' . $cIDSystemInfo->getSupplement() . ')';
        $commentLines[] = '%%Version: 1';
        $comments = implode("\n", $commentLines);

        $cMapHeaderLines = [];
        $cMapHeaderLines[] = '/CIDInit /ProcSet findresource begin'; // initializes cmap routines
        $cMapHeaderLines[] = '9 dict begin'; // ensure dictionary with 4 entries can be created. +5 due to bug in old PS interpreters
        $cMapHeaderLines[] = 'begincmap';
        $cMapHeaderLines[] = '/CIDSystemInfo 3 dict dup begin';
        $cMapHeaderLines[] = ' /Registry (' . $cIDSystemInfo->getRegistry() . ') def';
        $cMapHeaderLines[] = ' /Ordering (' . $cIDSystemInfo->getOrdering() . ') def';
        $cMapHeaderLines[] = ' /Supplement (' . $cIDSystemInfo->getSupplement() . ') def';
        $cMapHeaderLines[] = 'end def';
        $cMapHeaderLines[] = '/CMapName /' . $cMapName . ' def';
        $cMapHeaderLines[] = '/CMapType 0 def'; // implemented type of CMap (still current)
        /*
         * omit XUID & UIDOffset because no longer required
         * https://blogs.adobe.com/CCJKType/2016/06/no-more-xuid-arrays.html
         */
        $cMapHeaderLines[] = '/VMode 0 def'; // write horizontally
        $cMapHeader = implode("\n", $cMapHeaderLines);

        return $comments . "\n" . $cMapHeader;
    }

    /**
     * @param CharacterMapping[] $characterMapping
     *
     * @return string
     */
    private function getCMapCodeSpaces(array $characterMapping)
    {
        $entries = [];
        foreach ($characterMapping as $item) {
            $endByte = dechex($item->getEndByte());
            $byteLength = \strlen($endByte);

            $startByte = dechex($item->getStartByte());
            $adjustedStartByte = str_pad($startByte, $byteLength, '0', STR_PAD_LEFT);

            // TODO: can only map rectangle byte ranges; but this is not guaranteed by the input
            $entries[] = ' <' . $adjustedStartByte . '> <' . $endByte . '>';
        }

        $lines = [];
        $lines[] = \count($entries) . ' begincodespacerange';
        $lines = array_merge($lines, $entries);
        $lines[] = 'endcodespacerange';

        return implode("\n", $lines);
    }
}
