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

use PdfGenerator\Font\IR\CharacterRepository;
use PdfGenerator\Font\IR\Parser;
use PdfGenerator\Font\IR\Structure\Font;
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
        $parser = Parser::create();

        // act
        $font = $parser->parse(FileReaderTest::getDefaultFontContent());

        // assert
        $this->assertCount(885, $font->getCharacters());
        $this->assertSanityChecks($font);
    }

    private function assertSanityChecks(Font $font)
    {
        $characterRepo = new CharacterRepository($font);
        $oCharacter = $characterRepo->findByChar('o');
        $bigOCharacter = $characterRepo->findByChar('O');
        $mCharacter = $characterRepo->findByChar('m');

        $this->assertTrue($bigOCharacter->getBoundingBox()->getHeight() > $oCharacter->getBoundingBox()->getHeight());
        $this->assertTrue($mCharacter->getBoundingBox()->getWidth() > $oCharacter->getBoundingBox()->getWidth());
        $this->assertTrue($mCharacter->getGlyfTable()->getNumberOfContours() < $oCharacter->getGlyfTable()->getNumberOfContours());
    }
}
