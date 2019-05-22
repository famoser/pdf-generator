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
        $output = $writer->writeSubset($font->getFontFile(), [$character]);

        // assert
        file_put_contents(__DIR__ . \DIRECTORY_SEPARATOR . 'myfont.ttf', $output);
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
        $output = $writer->writeSubset($font->getFontFile(), [$character]);

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
        $output = $writer->writeSubset($font->getFontFile(), [$character]);
        $font = $parser->parse($output);
        $characterRepository = new CharacterRepository($font);
        $character = $characterRepository->find('g');
        $output2 = $writer->writeSubset($font->getFontFile(), [$character]);
        $font2 = $parser->parse($output2);

        // assert
        $this->assertSame($font, $font2);
    }

    /**
     * @throws \Exception
     */
    public function testNameTableEqualAfterSubsetting()
    {
        // arrange
        $parser = ParserTest::getParser();
        $font = $parser->parse(FileReaderTest::getDefaultFontContent());
        $writer = self::getFileWriter();
        $characterRepository = new CharacterRepository($font);
        $character = $characterRepository->find('g');

        // act
        $output = $writer->writeSubset($font->getFontFile(), [$character]);
        $font2 = $parser->parse($output);

        // assert
        $this->assertSame($font->getFontFile()->getNameTable(), $font2->getFontFile()->getNameTable());
        $this->assertSame($font->getFontFile()->getPrepTable(), $font2->getFontFile()->getPrepTable());
    }

    /**
     * @throws \Exception
     */
    public function ignored_testWithOracle()
    {
        // arrange
        $parser = ParserTest::getParser();
        $content = file_get_contents(__DIR__ . \DIRECTORY_SEPARATOR . 'OpenSans-Regular-subset.ttf');
        $font1 = $parser->parse($content);
        $characterRepository = new CharacterRepository($font1);
        $a1 = $characterRepository->find('a');

        // act
        $parser = ParserTest::getParser();
        $font2 = $parser->parse(FileReaderTest::getDefaultFontContent());
        $characterRepository = new CharacterRepository($font2);
        $a2 = $characterRepository->find('a');

        // assert
        $this->assertSame($font1, $font2);
        $this->assertSame($a1, $a2);
    }
}
