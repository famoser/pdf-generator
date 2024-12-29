<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Backend\Structure\Document\Font;

use Famoser\PdfGenerator\Backend\Catalog\Font\Structure\CIDSystemInfo;
use Famoser\PdfGenerator\Backend\Catalog\Font\Structure\CMap;
use Famoser\PdfGenerator\Font\IR\Structure\Character;

readonly class CMapCreator
{
    public function __construct(private bool $minimalCMapSize)
    {
    }

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
    public function createToUnicodeCMap(CIDSystemInfo $cIDSystemInfo, string $cMapName, array $characters): CMap
    {
        $byteMappings = $this->getCharacterIndexToUnicodeMappings($characters);

        return $this->createCMap($cIDSystemInfo, $cMapName, $byteMappings);
    }

    private function createCMap(CIDSystemInfo $cIDSystemInfo, string $cMapName, string $mappings): CMap
    {
        $header = $this->getCMapHeader($cIDSystemInfo, $cMapName);
        $trailer = $this->getCMapTrailer();
        $cMapData = $header."\n".$mappings."\n".$trailer;

        return new CMap($cMapName, $cIDSystemInfo, $cMapData);
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
        $textInHexToUnicodeMappingByLength = $this->getTextInHexToUnicodeMappingByLength($characters);

        if ($this->minimalCMapSize) {
            $codeSpaceDictionaries = $this->getCodeSpaceOfLengths(array_keys($textInHexToUnicodeMappingByLength));
            $codeMappingDictionaries = $this->getUTF8BfRange($textInHexToUnicodeMappingByLength);
        } else {
            $codeSpaceDictionaries = $this->getCodeSpaceRange($textInHexToUnicodeMappingByLength);
            $codeMappingDictionaries = $this->getBfRange($textInHexToUnicodeMappingByLength);
        }

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
        $notDefRangeDictionaries = $this->getNotDefRange($characters, $usedCodepoints);

        return
            implode("\n\n", $codeSpaceDictionaries)."\n\n".
            implode("\n\n", $codeMappingDictionaries)."\n\n".
            implode("\n\n", $notDefRangeDictionaries);
    }

    /**
     * @param Character[] $characters
     * @param int[]       $usedCodepoints
     *
     * @return string[]
     */
    private function getNotDefRange(array $characters, array $usedCodepoints): array
    {
        /** @var int[] $existingCodepoint */
        $existingCodepoint = [];
        foreach ($characters as $character) {
            if (null !== $character->getUnicodePoint()) {
                $existingCodepoint[] = $character->getUnicodePoint();
            }
        }

        $codePointsWithoutCharacterIndex = array_diff($usedCodepoints, $existingCodepoint);

        // must always map 0 character, at first position
        if (0 === \count($codePointsWithoutCharacterIndex) || 0 !== $codePointsWithoutCharacterIndex[0]) {
            array_unshift($codePointsWithoutCharacterIndex, 0);
        }

        $notDefRanges = $this->getNotDefRanges($codePointsWithoutCharacterIndex, 0);

        return $this->toDictionary($notDefRanges, 'notdefrange');
    }

    /**
     * @param array<int, array<string, int>> $textInHexToCharacterIndexMappingByLength
     *
     * @return string[]
     */
    private function getCidRange(array $textInHexToCharacterIndexMappingByLength): array
    {
        $codeMappings = [];
        foreach ($textInHexToCharacterIndexMappingByLength as $textInHexToCharacterIndexMapping) {
            $sameLengthCidRanges = $this->getSameLengthCidRanges($textInHexToCharacterIndexMapping);
            $codeMappings = array_merge($codeMappings, $sameLengthCidRanges);
        }

        return $this->toDictionary($codeMappings, 'cidrange');
    }

    /**
     * @param array<int, array<string, int>> $textInHexToUnicodeMappingByLength
     *
     * @return string[]
     */
    private function getUTF8BfRange(array $textInHexToUnicodeMappingByLength): array
    {
        $bfRanges = [];
        foreach ($textInHexToUnicodeMappingByLength as $textInHexToUnicodeMapping) {
            $sameLengthBfRanges = $this->getSameLengthUTF8BfRanges($textInHexToUnicodeMapping);
            $bfRanges = array_merge($bfRanges, $sameLengthBfRanges);
        }

        return $this->toDictionary($bfRanges, 'bfrange');
    }

    /**
     * @param array<int, array<string, int>> $textInHexToUnicodeMappingByLength
     *
     * @return string[]
     */
    private function getBfRange(array $textInHexToUnicodeMappingByLength): array
    {
        $bfRanges = [];
        foreach ($textInHexToUnicodeMappingByLength as $textInHexToUnicodeMapping) {
            $sameLengthBfRanges = $this->getSameLengthBfRanges($textInHexToUnicodeMapping);
            $bfRanges = array_merge($bfRanges, $sameLengthBfRanges);
        }

        return $this->toDictionary($bfRanges, 'bfrange');
    }

    /**
     * @template T
     *
     * @param array<int, array<string, T>> $hexKeysByLength
     *
     * @return string[]
     */
    private function getCodeSpaceRange(array $hexKeysByLength): array
    {
        $codeSpaces = [];
        foreach ($hexKeysByLength as $textInHexToCharacterIndexMapping) {
            ksort($textInHexToCharacterIndexMapping);

            $sameLengthCodeSpaceRanges = $this->getSameLengthCodeSpaceRanges(array_keys($textInHexToCharacterIndexMapping));
            $codeSpaces = array_merge($codeSpaces, $sameLengthCodeSpaceRanges);
        }

        return $this->toDictionary($codeSpaces, 'codespacerange');
    }

    /**
     * @param string[] $entries
     * @param int<1, max> $maxEntries
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
     *
     * @return array<int, array<string, int>>
     */
    private function getTextInHexToCharacterIndexMappingByLength(array $characters): array
    {
        $unicodeMapping = [];
        $characterCount = \count($characters);
        for ($i = 0; $i < $characterCount; ++$i) {
            $character = $characters[$i];
            if (null === $character->getUnicodePoint()) {
                continue;
            }

            $unicodeMapping[$character->getUnicodePoint()] = $i;
        }

        return $this->createMappingByHexLength($unicodeMapping);
    }

    /**
     * @param Character[] $characters
     *
     * @return array<int, array<string, int>>
     */
    private function getTextInHexToUnicodeMappingByLength(array $characters): array
    {
        $unicodeMapping = [];
        foreach ($characters as $character) {
            if (null === $character->getUnicodePoint()) {
                continue;
            }

            $unicodeMapping[$character->getUnicodePoint()] = $character->getUnicodePoint();
        }

        return $this->createMappingByHexLength($unicodeMapping);
    }

    /**
     * @template T
     *
     * @param array<int, T> $unicodeMapping
     *
     * @return array<int, array<string, T>>
     */
    private function createMappingByHexLength(array $unicodeMapping): array
    {
        /** @var array<int, array<string, T>> $hexPointsByLength */
        $hexPointsByLength = [];
        foreach ($unicodeMapping as $unicodePoint => $value) {
            $utf8Char = mb_chr($unicodePoint, 'UTF-8');
            $byte = unpack('H*', $utf8Char)[1];
            $normalizedByte = $this->ensureLengthMultipleOf2($byte);
            $length = \strlen($normalizedByte);

            if (!array_key_exists($length, $hexPointsByLength)) {
                $hexPointsByLength[$length] = [];
            }

            $hexPointsByLength[$length][$normalizedByte] = $value;
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
     * @param int[] $lengths
     *
     * @return string[]
     */
    private function getCodeSpaceOfLengths(array $lengths): array
    {
        $codeSpaces = [];
        foreach ($lengths as $length) {
            $firstHexPoint = str_repeat('0', $length);
            $lastHexPoint = str_repeat('f', $length);

            $codeSpaces[] = '<'.$firstHexPoint.'> <'.$lastHexPoint.'>';
        }

        return $this->toDictionary($codeSpaces, 'codespacerange');
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
     * @param array<string, int> $hexValueToCharacterIndexMapping
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
     * @param array<int> $textInHexToUnicodeMapping
     *
     * @return string[]
     */
    private function getSameLengthBfRanges(array $textInHexToUnicodeMapping): array
    {
        $firstUnicode = null;
        $firstHex = null;

        $expectedByte = null;
        $expectedUnicode = null;

        $lastHex = null;
        $codeMappings = [];
        foreach ($textInHexToUnicodeMapping as $hex => $unicode) {
            $byte = hexdec($hex);

            if ($unicode !== $expectedUnicode || $expectedByte !== $byte) {
                if ($firstHex && $lastHex && $firstUnicode) {
                    $codeMappings[] = '<'.$firstHex.'> <'.$lastHex.'> <'.dechex($firstUnicode).'>';
                }

                $expectedByte = $byte;
                $expectedUnicode = $unicode;
                $firstHex = $hex;
                $firstUnicode = $unicode;
            }

            ++$expectedByte;
            ++$expectedUnicode;
            $lastHex = $hex;
        }

        if ($firstHex && $lastHex && $firstUnicode) {
            $codeMappings[] = '<'.$firstHex.'> <'.$lastHex.'> <'.dechex($firstUnicode).'>';
        }

        return $codeMappings;
    }

    /**
     * @param array<int> $textInHexToUnicodeMapping
     *
     * @return string[]
     */
    private function getSameLengthUTF8BfRanges(array $textInHexToUnicodeMapping): array
    {
        /*
         * The CMap character encoding chosen follows UTF-8
         * We may use therefore use the structure of UTF-8 to efficiently define the cmap
         * Concretely, we only need one Bf range for each UTF-8 range (all lower content bits until the first structure bit)
         */

        if (0 === count($textInHexToUnicodeMapping)) {
            return [];
        }

        $firstHexValue = array_key_first($textInHexToUnicodeMapping);
        if (2 === strlen($firstHexValue)) {
            return ['<00> <7F> <00>']; // single-byte range has single structural bit in 8th position
        }

        $blockSize = 63; // for all byte lengths > 1, the first structural bit is in the 7th position

        $currentBlock = null;
        $blocks = [];
        foreach ($textInHexToUnicodeMapping as $hex => $unicode) {
            /** @var int $byte */
            $byte = hexdec($hex);
            $block = $byte - ($byte & $blockSize);
            if (null === $currentBlock || $currentBlock !== $block) {
                $blocks[] = $block;
                $currentBlock = $block;
            }
        }

        $codeMappings = [];
        foreach ($blocks as $block) {
            $endOfBlock = $block | $blockSize;
            $firstUnicodeOfBlock = $this->convertUTF8IntegerToUnicode($block);
            $codeMappings[] = '<'.dechex($currentBlock).'> <'.dechex($endOfBlock).'> <'.dechex($firstUnicodeOfBlock).'>';
        }

        return $codeMappings;
    }

    private function convertUTF8IntegerToUnicode(int $integer): int
    {
        $binary = '';
        $pendingBytes = $integer;
        while ($pendingBytes > 0) {
            $firstByte = $pendingBytes & 255;
            $binary = pack('C', $firstByte).$binary;
            $pendingBytes = $pendingBytes >> 8;
        }

        return mb_ord($binary, 'UTF-8');
    }

    /**
     * @param int[] $codePointsWithoutCharacterIndex
     *
     * @return string[]
     */
    private function getNotDefRanges(array $codePointsWithoutCharacterIndex, int $notDefCharacterIndex): array
    {
        $codeMappings = [];

        $startCodePoint = null;
        $endCodePoint = null;

        $entries = [];
        foreach ($codePointsWithoutCharacterIndex as $codePoint) {
            if ($codePoint - 1 !== $endCodePoint) {
                if (null !== $startCodePoint) {
                    $entries[$startCodePoint] = $endCodePoint;
                }

                $startCodePoint = $codePoint;
            }

            $endCodePoint = $codePoint;
        }

        if ($startCodePoint) {
            $entries[$startCodePoint] = $endCodePoint;
        }

        foreach ($entries as $start => $end) {
            $codeMappings[] = '<'.dechex($start).'> <'.dechex($end).'> '.$notDefCharacterIndex;
        }

        return $codeMappings;
    }
}
