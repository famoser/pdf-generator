<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Tests\Integration\Font\Backend;

use PdfGenerator\Font\Backend\FileWriter;
use PdfGenerator\Font\IR\CharacterRepository;
use PdfGenerator\Font\IR\Optimizer;
use PdfGenerator\Font\IR\Parser;
use PdfGenerator\Font\IR\Structure\Font;
use PdfGenerator\Tests\Integration\Font\Frontend\FileReaderTest;
use PHPUnit\Framework\TestCase;

class FileWriterTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testWriteSubset()
    {
        // arrange
        $parser = Parser::create();
        $font = $parser->parse(FileReaderTest::getDefaultFontContent());
        $writer = FileWriter::create();
        $subset = self::getFontSubset($font, 'g');

        // act
        $output = $writer->writeFont($subset);

        // assert
        $this->assertStringContainsString($subset->getCharacters()[0]->getGlyfTable()->getContent(), $output);
    }

    /**
     * @throws \Exception
     */
    public function testWriteSubsetMultipleCharacters()
    {
        // arrange
        $parser = Parser::create();
        $font = $parser->parse(FileReaderTest::getDefaultFontContent());
        $writer = FileWriter::create();
        $characterRepository = new CharacterRepository($font);
        $character = $characterRepository->findByChar('O');
        $character1 = $characterRepository->findByChar('o');
        $character2 = $characterRepository->findByChar('m');
        $optimizer = new Optimizer();
        $subset = $optimizer->getFontSubset($font, [$character, $character1, $character2]);

        // act
        $output = $writer->writeFont($subset);

        // assert
        $this->assertStringContainsString($character->getGlyfTable()->getContent(), $output);
        $this->assertStringContainsString($character1->getGlyfTable()->getContent(), $output);
        $this->assertStringContainsString($character2->getGlyfTable()->getContent(), $output);
    }

    /**
     * @throws \Exception
     */
    public function testReadSubset()
    {
        // arrange
        $parser = Parser::create();
        $font = $parser->parse(FileReaderTest::getDefaultFontContent());
        $writer = FileWriter::create();
        $subset = $this->getFontSubset($font, 'g', ' ');

        // act
        $output = $writer->writeFont($subset);

        // assert
        $font = $parser->parse($output);
        $characterRepository = new CharacterRepository($font);
        $this->assertNotNull($characterRepository->findByChar('g'));
        $this->assertNotNull($characterRepository->findByChar(' '));
        $this->assertNull($characterRepository->findByChar('o'));
    }

    /**
     * @throws \Exception
     */
    public function testRepeatedSubsettingProducesSameFile()
    {
        // arrange
        $parser = Parser::create();
        $font = $parser->parse(FileReaderTest::getDefaultFontContent());
        $writer = FileWriter::create();
        $subset = $this->getFontSubset($font, 'g');

        // act
        $output = $writer->writeFont($subset);
        $font = $parser->parse($output);

        $subset2 = $this->getFontSubset($font, 'g');
        $output2 = $writer->writeFont($subset2);
        $font2 = $parser->parse($output2);

        // assert
        $this->assertEquals($font, $font2);
    }

    /**
     * @throws \Exception
     */
    public function testSomeTablesEqualAfterSubsetting()
    {
        // arrange
        $parser = Parser::create();
        $font = $parser->parse(FileReaderTest::getDefaultFontContent());
        $writer = FileWriter::create();
        $subset = self::getFontSubset($font, 'g');

        // act
        $output = $writer->writeFont($subset);
        $font2 = $parser->parse($output);

        // assert
        $this->assertEquals($font->getTableDirectory()->getNameTable(), $font2->getTableDirectory()->getNameTable());
        $this->assertEquals($font->getTableDirectory()->getPrepTable(), $font2->getTableDirectory()->getPrepTable());
    }

    /**
     * @throws \Exception
     */
    public function testSubsetComponentGlyphs()
    {
        // arrange
        $parser = Parser::create();
        $font = $parser->parse(FileReaderTest::getDefaultFontContent());
        $writer = FileWriter::create();
        $subset = self::getFontSubset($font, 'Ä');

        // act
        $output = $writer->writeFont($subset);
        $font = $parser->parse($output);
        $output2 = $writer->writeFont($font);

        // assert
        $this->assertStringContainsString($subset->getCharacters()[0]->getGlyfTable()->getContent(), $output);
        $this->assertEquals($output, $output2);
    }

    /**
     * @throws \Exception
     */
    private static function getFontSubset(Font $font, ...$characters): Font
    {
        $characterRepository = new CharacterRepository($font);
        $resultingCharacters = [];
        foreach ($characters as $character) {
            $resultingCharacters[] = $characterRepository->findByChar($character);
        }

        $optimizer = new Optimizer();

        return $optimizer->getFontSubset($font, $resultingCharacters);
    }
}
