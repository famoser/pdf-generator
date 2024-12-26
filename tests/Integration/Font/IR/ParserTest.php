<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Tests\Integration\Font\IR;

use Famoser\PdfGenerator\Font\IR\CharacterRepository;
use Famoser\PdfGenerator\Font\IR\Parser;
use Famoser\PdfGenerator\Font\IR\Structure\Font;
use Famoser\PdfGenerator\Tests\Integration\Font\Frontend\FileReaderTest;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testParse(): void
    {
        // arrange
        $parser = Parser::create();

        // act
        $font = $parser->parse(FileReaderTest::getDefaultFontContent());

        // assert
        $this->assertCount(935, $font->getCharacters());
        $this->assertSanityChecks($font);
    }

    private function assertSanityChecks(Font $font): void
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
