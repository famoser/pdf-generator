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
     * @var Factory
     */
    private $factory;

    /**
     * GlyphIndexFormatVisitor constructor.
     *
     * @param Factory $factory
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param Format $format
     *
     * @return GlyphInfo[]
     */
    public function visitFormat(Format $format)
    {
        return $format->accept($this);
    }

    /**
     * @param Format1 $format1
     *
     * @return mixed
     */
    public function visitFormat1(Format1 $format1)
    {
        $macintoshMapping = $this->factory->getMacintoshMapping();

        $result = [];
        foreach ($macintoshMapping as $glyphIndex => $name) {
            $result[] = self::createGlyphInfo($glyphIndex, $name);
        }

        return $result;
    }

    /**
     * @param int|null $glyphIndex
     * @param string|null $name
     *
     * @return GlyphInfo
     */
    private static function createGlyphInfo(?int $glyphIndex, ?string $name)
    {
        $glyphInfo = new GlyphInfo();
        $glyphInfo->setMacintoshIndex($glyphIndex);
        $glyphInfo->setName($name);

        return $glyphInfo;
    }

    /**
     * @param Format2 $format2
     *
     * @return GlyphInfo[]
     */
    public function visitFormat2(Format2 $format2)
    {
        $macintoshMapping = $this->factory->getMacintoshMapping();
        $names = $this->streamToPascalStrings($format2->getNames());

        $result = [];
        for ($i = 0; $i < $format2->getNumGlyphs(); ++$i) {
            $index = $format2->getGlyphNameIndex()[$i];
            if ($index < 258) {
                $result[] = self::createGlyphInfo($index, $macintoshMapping[$index]);
            } else {
                $nameIndex = $index -= 258;
                $name = $names[$nameIndex];
                $result[] = self::createGlyphInfo($index, $name);
            }
        }

        return $result;
    }

    /**
     * @param string $stream
     *
     * @return array
     */
    private function streamToPascalStrings(string $stream)
    {
        $length = \strlen($stream);
        $activeIndex = 0;

        $result = [];
        while ($activeIndex < $length) {
            $stringLength = (int)$stream[$activeIndex];
            $result[] = substr($stream, $activeIndex + 1, $stringLength);

            $activeIndex += $stringLength + 1;
        }

        return $result;
    }

    /**
     * @param Format25 $format25
     *
     * @return mixed
     */
    public function visitFormat25(Format25 $format25)
    {
        $macintoshMapping = $this->factory->getMacintoshMapping();

        $result = [];
        for ($i = 0; $i < $format25->getNumGlyphs(); ++$i) {
            $offset = $format25->getOffsets()[$i];

            $macintoshOrdering = $i + $offset;
            $result[] = self::createGlyphInfo($macintoshOrdering, $macintoshMapping[$macintoshOrdering]);
        }

        return $result;
    }

    /**
     * @param Format3 $format3
     *
     * @return mixed
     */
    public function visitFormat3(Format3 $format3)
    {
        return [];
    }
}
