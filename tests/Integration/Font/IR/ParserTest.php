<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Tests\Integration\Font\IR;

use PdfGenerator\Font\Frontend\Utils\GlyphIndexFormatVisitor;
use PdfGenerator\Font\IR\Parser;
use PdfGenerator\Font\IR\Structure\Character;
use PdfGenerator\Tests\Integration\Font\Frontend\FileReaderTest;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testParse()
    {
        // arrange
        $glyphIndexFormatVisitor = new GlyphIndexFormatVisitor();
        $parser = new Parser($glyphIndexFormatVisitor);

        // act
        $font = $parser->parse(FileReaderTest::getDefaultFontContent());

        // assert
        $this->assertCount(869, $font->getCharacters());
        $this->assertSanityChecks($font->getCharacters());
    }

    /**
     * @param Character[] $characters
     */
    private function assertSanityChecks(array $characters)
    {
        $oCharacter = $characters[mb_ord('o')];
        $bigOCharacter = $characters[mb_ord('O')];
        $mCharacter = $characters[mb_ord('m')];

        $this->assertTrue($bigOCharacter->getBoundingBox()->getHeight() > $oCharacter->getBoundingBox()->getHeight());
        $this->assertTrue($mCharacter->getBoundingBox()->getWidth() > $oCharacter->getBoundingBox()->getWidth());
        $this->assertTrue($mCharacter->getGlyfTable()->getNumberOfContours() > $oCharacter->getGlyfTable()->getNumberOfContours());
    }
}
