<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Tests\Unit\Font\Resources\GlyphNameMapping;

use PdfGenerator\Font\Resources\GlyphNameMapping\Factory;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    public static function getFactory(): Factory
    {
        return new Factory();
    }

    /**
     * basic sanity check for some known characters.
     */
    public function testAGLFMappingSomeSensibleValues(): void
    {
        // arrange
        $factory = self::getFactory();

        // act
        $mapping = $factory->getAGLFMapping();

        // assert
        $expectedMapping = [
            'a' => 'a',
            'A' => 'A',
            '\\' => 'backslash',
            'ä' => 'adieresis',
        ];

        foreach ($expectedMapping as $key => $value) {
            $character = mb_ord($key, 'UTF-8');
            $this->assertEquals($value, $mapping[$character]);
        }
    }

    /**
     * basic sanity check for some known characters.
     */
    public function testMacintoshMappingSomeSensibleValues(): void
    {
        // arrange
        $factory = self::getFactory();

        // act
        $mapping = $factory->getMacintoshMapping();

        // assert
        $this->assertCount(258, $mapping);

        $expectedMapping = [
            0 => '.notdef',
            201 => 'Aacute',
            257 => 'dcroat',
        ];

        foreach ($expectedMapping as $key => $value) {
            $this->assertEquals($value, $mapping[$key]);
        }
    }
}
