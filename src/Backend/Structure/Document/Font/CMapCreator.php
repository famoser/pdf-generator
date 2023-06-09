<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Document\Font;

use PdfGenerator\Backend\Catalog\Font\Structure\CIDSystemInfo;
use PdfGenerator\Backend\Catalog\Font\Structure\CMap;
use PdfGenerator\Font\IR\Structure\Character;

class CMapCreator
{
    /**
     * @param Character[] $characters
     * @param int[]       $usedCodepoints
     */
    public function createTextToCharacterIndexCMap(CIDSystemInfo $cIDSystemInfo, string $cMapName, array $characters, array $usedCodepoints): CMap
    {
        $byteMappings = $this->getTextToCharacterIndexMappings($characters, $usedCodepoints);

        return $this->createCMap($cIDSystemInfo, $cMapName, $byteMappings);
    }

    /**
     * @param Character[] $characters
     */
    public function createCharacterIndexToUnicodeCMap(CIDSystemInfo $cIDSystemInfo, string $cMapName, array $characters): CMap
    {
        $byteMappings = $this->getCharacterIndexToUnicodeMappings($characters);

        return $this->createCMap($cIDSystemInfo, $cMapName, $byteMappings);
    }

    private function createCMap(CIDSystemInfo $cIDSystemInfo, string $cMapName, string $mappings): CMap
    {
        $cmap = new CMap();
        $cmap->setCIDSystemInfo($cIDSystemInfo);
        $cmap->setCMapName($cMapName);

        $header = $this->getCMapHeader($cIDSystemInfo, $cMapName);
        $trailer = $this->getCMapTrailer();

        $cMapData = $header."\n".$mappings."\n".$trailer;
        $cmap->setCMapData($cMapData);

        return $cmap;
    }

    private function getCMapHeader(CIDSystemInfo $cIDSystemInfo, string $cMapName): string
    {
        $commentLines = [];
        $commentLines[] = '%!PS-Adobe-3.0 Resource-CMap';
        $commentLines[] = '%%DocumentNeededResources: procset CIDInit';
        $commentLines[] = '%%IncludeResource: procset CIDInit';
        $commentLines[] = '%%BeginResource: CMap '.$cMapName;
        $commentLines[] = '%%Title: ('.$cMapName.' '.$cIDSystemInfo->getRegistry().' '.$cIDSystemInfo->getOrdering().' '.$cIDSystemInfo->getSupplement().')';
        $commentLines[] = '%%Version: 1';
        $comments = implode("\n", $commentLines);

        $cMapHeaderLines = [];
        $cMapHeaderLines[] = '/CIDInit /ProcSet findresource begin'; // initializes cmap routines
        $cMapHeaderLines[] = '9 dict begin'; // ensure dictionary with 4 entries can be created. +5 due to bug in old PS interpreters
        $cMapHeaderLines[] = 'begincmap';
        $cMapHeaderLines[] = '/CIDSystemInfo 3 dict dup begin';
        $cMapHeaderLines[] = ' /Registry ('.$cIDSystemInfo->getRegistry().') def';
        $cMapHeaderLines[] = ' /Ordering ('.$cIDSystemInfo->getOrdering().') def';
        $cMapHeaderLines[] = ' /Supplement ('.$cIDSystemInfo->getSupplement().') def';
        $cMapHeaderLines[] = 'end def';
        $cMapHeaderLines[] = '/CMapName /'.$cMapName.' def';
        $cMapHeaderLines[] = '/CMapType 0 def'; // implemented type of CMap (still current)
        /*
         * omit XUID & UIDOffset because no longer required
         * https://blogs.adobe.com/CCJKType/2016/06/no-more-xuid-arrays.html
         */
        $cMapHeaderLines[] = '/VMode 0 def'; // write horizontally
        $cMapHeader = implode("\n", $cMapHeaderLines);

        return $comments."\n".$cMapHeader;
    }

    private function getCMapTrailer(): string
    {
        $tralerLines = [];
        $tralerLines[] = 'endcmap';
        $tralerLines[] = 'CMapName currentdict /CMap defineresource pop';
        $tralerLines[] = 'end';
        $tralerLines[] = 'end';
        $tralerLines[] = '%%EndResource';
        $tralerLines[] = '%%EOF';

        return implode("\n", $tralerLines);
    }

    /**
     * @param Character[] $characters
     */
    private function getCharacterIndexToUnicodeMappings(array $characters): string
    {
        $characterIndexToUnicodeMappingInHexByLength = $this->getCharacterIndexToUnicodeMappingInHexByLength($characters);

        $codeSpaceDictionaries = $this->getCodeSpaceRange($characterIndexToUnicodeMappingInHexByLength);
        $codeMappingDictionaries = $this->getBfRange($characterIndexToUnicodeMappingInHexByLength);

        return
            implode("\n\n", $codeSpaceDictionaries)."\n\n".
            implode("\n\n", $codeMappingDictionaries);
    }

    /**
     * @param Character[] $characters
     * @param int[]       $usedCodepoints
     */
    private function getTextToCharacterIndexMappings(array $characters, array $usedCodepoints): string
    {
        $textInHexToCharacterIndexMappingByLength = $this->getTextInHexToCharacterIndexMappingByLength($characters);

        $codeSpaceDictionaries = $this->getCodeSpaceRange($textInHexToCharacterIndexMappingByLength);
        $codeMappingDictionaries = $this->getCidRange($textInHexToCharacterIndexMappingByLength);

        $existingCodepoint = [];
        foreach ($characters as $character) {
            if (null !== $character->getUnicodePoint()) {
                $existingCodepoint[] = $character->getUnicodePoint();
            }
        }

        $notDefinedCodepoints = array_diff($usedCodepoints, $existingCodepoint);
        $notDefRangeDictionaries = $this->getNotDefRange($notDefinedCodepoints);

        return
            implode("\n\n", $codeSpaceDictionaries)."\n\n".
            implode("\n\n", $codeMappingDictionaries)."\n\n".
            implode("\n\n", $notDefRangeDictionaries);
    }

    private function getNotDefRange(array $codePointsWithoutCharacterIndex): array
    {
        // must always map 0 character
        if (0 === \count($codePointsWithoutCharacterIndex) || 0 !== $codePointsWithoutCharacterIndex[0]) {
            $codePointsWithoutCharacterIndex = array_merge([0], $codePointsWithoutCharacterIndex);
        }

        $notDefRanges = $this->getNotDefRanges($codePointsWithoutCharacterIndex, 0);

        return $this->toDictionary($notDefRanges, 'notdefrange');
    }

    private function getCidRange($textInHexToCharacterIndexMappingByLength): array
    {
        $codeMappings = [];
        foreach ($textInHexToCharacterIndexMappingByLength as $length => $textInHexToCharacterIndexMapping) {
            $codeMappings = array_merge($codeMappings, $this->getSameLengthCidRanges($textInHexToCharacterIndexMapping));
        }

        return $this->toDictionary($codeMappings, 'cidrange');
    }

    private function getBfRange($characterIndexToUnicodeMappingInHexByLength): array
    {
        $bfRanges = [];
        foreach ($characterIndexToUnicodeMappingInHexByLength as $length => $characterIndexToUnicodeMappingInHex) {
            $bfRanges = array_merge($bfRanges, $this->getSameLengthBfRanges($characterIndexToUnicodeMappingInHex));
        }

        return $this->toDictionary($bfRanges, 'bfrange');
    }

    private function getCodeSpaceRange(array $hexKeysByLength): array
    {
        $codeSpaces = [];
        foreach ($hexKeysByLength as $length => $textInHexToCharacterIndexMapping) {
            ksort($textInHexToCharacterIndexMapping);

            $codeSpaces = array_merge($codeSpaces, $this->getSameLengthCodeSpaceRanges(array_keys($textInHexToCharacterIndexMapping)));
        }

        return $this->toDictionary($codeSpaces, 'codespacerange');
    }

    /**
     * @param string[] $entries
     *
     * @return string[]
     */
    private function toDictionary(array $entries, string $identifier, int $maxEntries = 100): array
    {
        $dictionaries = [];
        foreach (array_chunk($entries, $maxEntries) as $currentEntries) {
            $dictionary = \count($currentEntries).' begin'.$identifier."\n";
            $dictionary .= implode("\n", $currentEntries)."\n";
            $dictionary .= 'end'.$identifier;

            $dictionaries[] = $dictionary;
        }

        return $dictionaries;
    }

    /**
     * @param Character[] $characters
     */
    private function getCharacterIndexToUnicodeMappingInHexByLength(array $characters): array
    {
        $hexPointsByLength = [];
        $characterCount = \count($characters);
        for ($i = 0; $i < $characterCount; ++$i) {
            $character = $characters[$i];
            if (null === $character->getUnicodePoint()) {
                continue;
            }

            $utf16BEChar = mb_chr($character->getUnicodePoint(), 'UTF-16BE');
            $byte = unpack('H*', $utf16BEChar)[1];
            $normalizedByte = $this->ensureLengthMultipleOf2($byte);

            $characterByte = dechex($i);
            $normalizedCharacterByte = $this->ensureLengthMultipleOf2($characterByte);
            $length = \strlen($normalizedCharacterByte);
            if (!isset($hexPointsByLength[$length])) {
                $hexPointsByLength[$length] = [];
            }

            $hexPointsByLength[$length][$normalizedCharacterByte] = $normalizedByte;
        }

        ksort($hexPointsByLength);

        return $hexPointsByLength;
    }

    /**
     * @param Character[] $characters
     */
    private function getTextInHexToCharacterIndexMappingByLength(array $characters): array
    {
        $hexPointsByLength = [];
        $characterCount = \count($characters);
        for ($i = 0; $i < $characterCount; ++$i) {
            $character = $characters[$i];
            if (null === $character->getUnicodePoint()) {
                continue;
            }

            $utf8Char = mb_chr($character->getUnicodePoint(), 'UTF-8');
            $byte = unpack('H*', $utf8Char)[1];
            $normalizedByte = $this->ensureLengthMultipleOf2($byte);
            $length = \strlen($normalizedByte);

            if (!isset($hexPointsByLength[$length])) {
                $hexPointsByLength[$length] = [];
            }

            $hexPointsByLength[$length][$normalizedByte] = $i;
        }

        ksort($hexPointsByLength);

        return $hexPointsByLength;
    }

    private function ensureLengthMultipleOf2(string $byte): string
    {
        $length = \strlen($byte);
        if (0 !== $length % 2) {
            return '0'.$byte;
        }

        return $byte;
    }

    /**
     * @param string[] $sameLengthHexPoints
     *
     * @return string[]
     */
    private function getSameLengthCodeSpaceRanges(array $sameLengthHexPoints): array
    {
        $codeSpaces = [];

        $lastValue = null;
        $firstHexPoint = null;
        $lastHexPoint = null;
        foreach ($sameLengthHexPoints as $hexPoint) {
            $currentValue = hexdec($hexPoint);

            if ($currentValue - 1 !== $lastValue) {
                if (null !== $firstHexPoint) {
                    $codeSpaces[] = '<'.$firstHexPoint.'> <'.$lastHexPoint.'>';
                }
                $firstHexPoint = $hexPoint;
            }

            $lastHexPoint = $hexPoint;
            $lastValue = $currentValue;
        }

        $codeSpaces[] = '<'.$firstHexPoint.'> <'.$lastHexPoint.'>';

        return $codeSpaces;
    }

    /**
     * @param string[] $hexValueToCharacterIndexMapping
     *
     * @return string[]
     */
    private function getSameLengthCidRanges(array $hexValueToCharacterIndexMapping): array
    {
        $codeMappings = [];

        $lastValue = null;
        $lastCharacterIndex = null;
        $firstHexPoint = null;
        $firstCharacterIndex = null;
        $lastHexPoint = null;
        foreach ($hexValueToCharacterIndexMapping as $hexPoint => $characterIndex) {
            $currentValue = hexdec($hexPoint);

            if ($currentValue - 1 !== $lastValue || $characterIndex - 1 !== $lastCharacterIndex) {
                if (null !== $firstHexPoint) {
                    $codeMappings[] = '<'.$firstHexPoint.'> <'.$lastHexPoint.'> '.$firstCharacterIndex;
                }
                $firstHexPoint = $hexPoint;
                $firstCharacterIndex = $characterIndex;
            }

            $lastHexPoint = $hexPoint;
            $lastValue = $currentValue;
            $lastCharacterIndex = $characterIndex;
        }

        $codeMappings[] = '<'.$firstHexPoint.'> <'.$lastHexPoint.'> '.$firstCharacterIndex;

        return $codeMappings;
    }

    /**
     * @return string[]
     */
    private function getSameLengthBfRanges(array $characterIndexToUnicodeMappingInHex): array
    {
        $codeMappings = [];

        $lastCharacterIndex = null;
        $lastUnicodeValue = null;
        $firstHexPoint = null;
        $firstUnicodeHex = null;
        $lastHexPoint = null;
        foreach ($characterIndexToUnicodeMappingInHex as $characterIndexHex => $unicodeHex) {
            $characterIndex = hexdec($characterIndexHex);
            $unicodeValue = hexdec((string) $unicodeHex);

            if ($characterIndex - 1 !== $lastCharacterIndex || $unicodeValue - 1 !== $lastUnicodeValue) {
                if (null !== $firstHexPoint) {
                    $codeMappings[] = '<'.$firstHexPoint.'> <'.$lastHexPoint.'> <'.$firstUnicodeHex.'>';
                }
                $firstHexPoint = $characterIndexHex;
                $firstUnicodeHex = $unicodeHex;
            }

            $lastHexPoint = $characterIndexHex;
            $lastCharacterIndex = $characterIndex;
            $lastUnicodeValue = $unicodeValue;
        }

        $codeMappings[] = '<'.$firstHexPoint.'> <'.$lastHexPoint.'> <'.$firstUnicodeHex.'>';

        return $codeMappings;
    }

    /**
     * @param string[] $hexPoints
     *
     * @return string[]
     */
    private function getNotDefRanges(array $hexPoints, int $notDefCharacterIndex): array
    {
        $codeMappings = [];

        $lastValue = null;
        $firstHexPoint = null;
        $lastHexPoint = null;
        foreach ($hexPoints as $hexPoint) {
            $currentValue = hexdec($hexPoint);

            if ($currentValue - 1 !== $lastValue) {
                if (null !== $firstHexPoint) {
                    $codeMappings[] = '<'.$firstHexPoint.'> <'.$lastHexPoint.'> '.$notDefCharacterIndex;
                }
                $firstHexPoint = $hexPoint;
            }

            $lastHexPoint = $hexPoint;
            $lastValue = $currentValue;
        }

        $codeMappings[] = '<'.$firstHexPoint.'> <'.$lastHexPoint.'> '.$notDefCharacterIndex;

        return $codeMappings;
    }
}
