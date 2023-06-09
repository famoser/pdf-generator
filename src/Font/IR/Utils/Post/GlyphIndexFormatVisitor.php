<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\IR\Utils\Post;

use PdfGenerator\Font\Frontend\File\Table\Post\Format\Format;
use PdfGenerator\Font\Frontend\File\Table\Post\Format\Format1;
use PdfGenerator\Font\Frontend\File\Table\Post\Format\Format2;
use PdfGenerator\Font\Frontend\File\Table\Post\Format\Format25;
use PdfGenerator\Font\Frontend\File\Table\Post\Format\Format3;
use PdfGenerator\Font\Frontend\File\Table\Post\VisitorInterface;
use PdfGenerator\Font\Resources\GlyphNameMapping\Factory;

class GlyphIndexFormatVisitor implements VisitorInterface
{
    /**
     * GlyphIndexFormatVisitor constructor.
     */
    public function __construct(private readonly Factory $factory)
    {
    }

    /**
     * @return GlyphInfo[]
     */
    public function visitFormat(Format $format): array
    {
        return $format->accept($this);
    }

    public function visitFormat1(Format1 $format1): array
    {
        $macintoshMapping = $this->factory->getMacintoshMapping();

        $result = [];
        foreach ($macintoshMapping as $glyphIndex => $name) {
            $result[] = self::createGlyphInfo($name, $glyphIndex);
        }

        return $result;
    }

    /**
     * @return GlyphInfo[]
     */
    public function visitFormat2(Format2 $format2): array
    {
        $macintoshMapping = $this->factory->getMacintoshMapping();
        $names = $this->streamToPascalStrings($format2->getNames());

        $result = [];
        for ($i = 0; $i < $format2->getNumGlyphs(); ++$i) {
            $index = $format2->getGlyphNameIndex()[$i];
            if ($index < 258) {
                $result[] = self::createGlyphInfo($macintoshMapping[$index], $index);
            } else {
                $nameIndex = $index - 258;
                $name = $names[$nameIndex];
                $result[] = self::createGlyphInfo($name);
            }
        }

        return $result;
    }

    public function visitFormat25(Format25 $format25): array
    {
        $macintoshMapping = $this->factory->getMacintoshMapping();

        $result = [];
        for ($i = 0; $i < $format25->getNumGlyphs(); ++$i) {
            $offset = $format25->getOffsets()[$i];

            $macintoshOrdering = $i + $offset;
            $result[] = self::createGlyphInfo($macintoshMapping[$macintoshOrdering], $macintoshOrdering);
        }

        return $result;
    }

    public function visitFormat3(Format3 $format3): array
    {
        return [];
    }

    private static function createGlyphInfo(string $name, int $macintoshSetIndex = null): GlyphInfo
    {
        $glyphInfo = new GlyphInfo();
        $glyphInfo->setName($name);
        $glyphInfo->setMacintoshIndex($macintoshSetIndex);

        return $glyphInfo;
    }

    private function streamToPascalStrings(string $stream): array
    {
        $length = \strlen($stream);
        $activeIndex = 0;

        $result = [];
        while ($activeIndex < $length) {
            $stringLength = \ord($stream[$activeIndex]);
            $result[] = substr($stream, $activeIndex + 1, $stringLength);

            $activeIndex += $stringLength + 1;
        }

        return $result;
    }
}
