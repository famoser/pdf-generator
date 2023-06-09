<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Tests\Unit\Font\IR\Utils\CMap;

use PdfGenerator\Font\Frontend\File\Table\CMap\Format\Format4;
use PdfGenerator\Font\IR\Utils\CMap\GlyphIndexFormatVisitor;
use PHPUnit\Framework\TestCase;

class GlyphIndexFormatVisitorTest extends TestCase
{
    private readonly GlyphIndexFormatVisitor $visitor;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->visitor = new GlyphIndexFormatVisitor();
    }

    /**
     * format4 with idrangeoffset = 0.
     */
    public function testFormat4SingleSegment()
    {
        // arrange
        $format4 = new Format4();
        $format4->setSegCountX2(2);
        $format4->setStartCodes([50]);
        $format4->setEndCodes([52]);
        $format4->setIdDeltas([-10]);
        $format4->setIdRangeOffsets([0]);
        $format4->setGlyphIndexArray([]);

        // act
        $characterMapping = $this->visitor->visitFormat4($format4);

        // assert
        $this->assertCount(3, $characterMapping);
        $this->assertSame([50 => 40, 51 => 41, 52 => 42], $characterMapping);
    }

    /**
     * format4 with idrangeoffset != 0.
     */
    public function testFormat4SingleSegmentWithIdRangeOffset()
    {
        // arrange
        $format4 = new Format4();
        $format4->setSegCountX2(2);
        $format4->setStartCodes([50]);
        $format4->setEndCodes([52]);
        $format4->setIdDeltas([-10]);
        $format4->setIdRangeOffsets([2]);
        $format4->setGlyphIndexArray([3, 4, 5]);

        // act
        $characterMapping = $this->visitor->visitFormat4($format4);

        // assert
        $this->assertCount(3, $characterMapping);
        $this->assertSame([50 => 3, 51 => 4, 52 => 5], $characterMapping);
    }

    /**
     * format4 with idrangeoffset = 0.
     */
    public function testFormat4MultipleSegments()
    {
        // arrange
        $format4 = new Format4();
        $format4->setSegCountX2(4);
        $format4->setStartCodes([50, 60]);
        $format4->setEndCodes([52, 61]);
        $format4->setIdDeltas([-10, -10]);
        $format4->setIdRangeOffsets([0, 0]);
        $format4->setGlyphIndexArray([]);

        // act
        $characterMapping = $this->visitor->visitFormat4($format4);

        // assert
        $this->assertCount(5, $characterMapping);
        $this->assertSame([50 => 40, 51 => 41, 52 => 42, 60 => 50, 61 => 51], $characterMapping);
    }
}
