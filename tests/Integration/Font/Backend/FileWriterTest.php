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
use PdfGenerator\Font\Backend\TableWriter;
use PdfGenerator\Font\IR\CharacterRepository;
use PdfGenerator\Tests\Integration\Font\Frontend\FileReaderTest;
use PdfGenerator\Tests\Integration\Font\IR\ParserTest;
use PHPUnit\Framework\TestCase;

class FileWriterTest extends TestCase
{
    /**
     * @return FileWriter
     */
    public static function getFileWriter()
    {
        $tableWriter = new TableWriter();

        return new FileWriter($tableWriter);
    }

    /**
     * @throws \Exception
     */
    public function testWriteSubset()
    {
        // arrange
        $parser = ParserTest::getParser();
        $font = $parser->parse(FileReaderTest::getDefaultFontContent());
        $writer = self::getFileWriter();
        $characterRepository = new CharacterRepository($font);
        $character = $characterRepository->find('g');

        // act
        $output = $writer->writeFile($font, [$character]);

        // assert
        $this->assertStringContainsString($character->getGlyfTable()->getContent(), $output);
    }

    /**
     * @throws \Exception
     */
    public function testWriteSubset_multipleCharacters()
    {
        // arrange
        $parser = ParserTest::getParser();
        $font = $parser->parse(FileReaderTest::getDefaultFontContent());
        $writer = self::getFileWriter();
        $characterRepository = new CharacterRepository($font);
        $character = $characterRepository->find('g');
        $character1 = $characterRepository->find('a');
        $character2 = $characterRepository->find('x');

        // act
        $output = $writer->writeFile($font, [$character, $character1, $character2]);

        // assert
        $this->assertStringContainsString($character->getGlyfTable()->getContent(), $output);
    }

    /**
     * @throws \Exception
     */
    public function testReadSubset()
    {
        // arrange
        $parser = ParserTest::getParser();
        $font = $parser->parse(FileReaderTest::getDefaultFontContent());
        $writer = self::getFileWriter();
        $characterRepository = new CharacterRepository($font);
        $character = $characterRepository->find('g');

        // act
        $output = $writer->writeFile($font, [$character]);

        // assert
        $font = $parser->parse($output);
        $characterRepository = new CharacterRepository($font);
        $this->assertNotNull($characterRepository->find('g'));
    }

    /**
     * @throws \Exception
     */
    public function testRepeatedSubsettingProducesSameFile()
    {
        // arrange
        $parser = ParserTest::getParser();
        $font = $parser->parse(FileReaderTest::getDefaultFontContent());
        $writer = self::getFileWriter();
        $characterRepository = new CharacterRepository($font);
        $character = $characterRepository->find('g');

        // act
        $output = $writer->writeFile($font, [$character]);
        $font = $parser->parse($output);
        $characterRepository = new CharacterRepository($font);
        $character = $characterRepository->find('g');
        $output2 = $writer->writeFile($font, [$character]);
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
        $parser = ParserTest::getParser();
        $font = $parser->parse(FileReaderTest::getDefaultFontContent());
        $writer = self::getFileWriter();
        $characterRepository = new CharacterRepository($font);
        $character = $characterRepository->find('g');

        // act
        $output = $writer->writeFile($font, [$character]);
        $font2 = $parser->parse($output);

        // assert
        $this->assertEquals($font->getFontFile()->getNameTable(), $font2->getFontFile()->getNameTable());
        $this->assertEquals($font->getFontFile()->getPrepTable(), $font2->getFontFile()->getPrepTable());
    }
}
