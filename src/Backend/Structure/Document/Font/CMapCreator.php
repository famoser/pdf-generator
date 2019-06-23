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

class CMapCreator
{
    /**
     * @param CIDSystemInfo $cIDSystemInfo
     * @param string $cMapName
     * @param int[] $orderedCodePoints
     *
     * @return CMap
     */
    public function createCMap(CIDSystemInfo $cIDSystemInfo, string $cMapName, array $orderedCodePoints)
    {
        $cmap = new CMap();
        $cmap->setCIDSystemInfo($cIDSystemInfo);
        $cmap->setCMapName($cMapName);

        $header = $this->getCMapHeader($cIDSystemInfo, $cMapName);
        $byteMappings = $this->getCMapByteMappings($orderedCodePoints);
        $trailer = $this->getCMapTrailer();

        $cMapData = $header . "\n" . $byteMappings . "\n" . $trailer;
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
     * @return string
     */
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
     * @param int[] $codePoints
     *
     * @return string
     */
    private function getCMapByteMappings(array $codePoints)
    {
        $hexCodePointsByLength = $this->getAsHexByLength($codePoints);

        $codeSpaces = [];
        foreach ($hexCodePointsByLength as $length => $hexPoints) {
            ksort($hexPoints);

            $codeSpaces = array_merge($codeSpaces, $this->getCodeSpaces($hexPoints));
        }

        $codeSpaceDictionaries = $this->toDictionary($codeSpaces, 'codespacerange');

        $codeMappings = [];
        foreach ($hexCodePointsByLength as $length => $hexPoints) {
            ksort($hexPoints);

            $codeMappings = array_merge($codeMappings, $this->getCodeMappings($hexPoints));
        }

        $codeMappingDictionaries = $this->toDictionary($codeMappings, 'cidrange');

        $validMappings = implode("\n\n", $codeSpaceDictionaries) . "\n\n" . implode("\n\n", $codeMappingDictionaries);

        $notDefMapping = "1 beginnotdefrange\n <00> <00> 0\nendnotdefrange";

        return $validMappings . "\n\n" . $notDefMapping;
    }

    /**
     * @param string[] $entries
     * @param string $identifier
     * @param int $maxEntries
     *
     * @return string[]
     */
    private function toDictionary(array $entries, string $identifier, int $maxEntries = 100)
    {
        $dictionaries = [];
        foreach (array_chunk($entries, $maxEntries) as $currentEntries) {
            $dictionary = \count($currentEntries) . ' begin' . $identifier . "\n";
            $dictionary .= implode("\n", $currentEntries) . "\n";
            $dictionary .= 'end' . $identifier;

            $dictionaries[] = $dictionary;
        }

        return $dictionaries;
    }

    /**
     * @param array $codePoints
     *
     * @return array
     */
    private function getAsHexByLength(array $codePoints): array
    {
        $hexPointsByLength = [];
        $characterIndex = 1;
        foreach ($codePoints as $codePoint) {
            $byte = dechex($codePoint);
            $length = \strlen($byte);
            if ($length % 2 !== 0) {
                $byte = '0' . $byte;
                ++$length;
            }

            if (!isset($hexPointsByLength[$length])) {
                $hexPointsByLength[$length] = [];
            }

            $hexPointsByLength[$length][$byte] = $characterIndex++;
        }

        sort($hexPointsByLength);

        return $hexPointsByLength;
    }

    /**
     * @param string[] $codePoints
     *
     * @return string[]
     */
    private function getCodeSpaces(array $codePoints): array
    {
        $codeSpaces = [];

        $lastValue = null;
        $firstHexPoint = null;
        $lastHexPoint = null;
        foreach ($codePoints as $hexPoint => $characterIndex) {
            $currentValue = hexdec($hexPoint);

            if ($currentValue - 1 !== $lastValue) {
                if ($firstHexPoint !== null) {
                    $codeSpaces[] = '<' . $firstHexPoint . '> <' . $lastHexPoint . '>';
                }
                $firstHexPoint = $hexPoint;
            }

            $lastHexPoint = $hexPoint;
            $lastValue = $currentValue;
        }

        $codeSpaces[] = '<' . $firstHexPoint . '> <' . $lastHexPoint . '>';

        return $codeSpaces;
    }

    /**
     * @param string[] $hexPoints
     *
     * @return string[]
     */
    private function getCodeMappings(array $hexPoints): array
    {
        $codeMappings = [];

        $lastValue = null;
        $lastCharacterIndex = null;
        $firstHexPoint = null;
        $firstCharacterIndex = null;
        $lastHexPoint = null;
        foreach ($hexPoints as $hexPoint => $characterIndex) {
            $currentValue = hexdec($hexPoint);

            if ($currentValue - 1 !== $lastValue || $characterIndex - 1 !== $lastCharacterIndex) {
                if ($firstHexPoint !== null) {
                    $codeMappings[] = '<' . $firstHexPoint . '> <' . $lastHexPoint . '> ' . $firstCharacterIndex;
                }
                $firstHexPoint = $hexPoint;
                $firstCharacterIndex = $characterIndex;
            }

            $lastHexPoint = $hexPoint;
            $lastValue = $currentValue;
            $lastCharacterIndex = $characterIndex;
        }

        $codeMappings[] = '<' . $firstHexPoint . '> <' . $lastHexPoint . '> ' . $firstCharacterIndex;

        return $codeMappings;
    }
}
