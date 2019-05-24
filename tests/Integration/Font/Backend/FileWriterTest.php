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

use PdfGenerator\Font\Backend\File\Table\CMap\FormatVisitor;
use PdfGenerator\Font\Backend\File\TableVisitor;
use PdfGenerator\Font\Backend\FileWriter;
use PdfGenerator\Font\IR\CharacterRepository;
use PdfGenerator\Font\IR\Optimizer;
use PdfGenerator\Font\IR\Structure\Font;
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
        $cMapFormatVisitor = new FormatVisitor();
        $postFormatVisitor = new \PdfGenerator\Font\Backend\File\Table\Post\FormatVisitor();
        $tableVisitor = new TableVisitor($cMapFormatVisitor, $postFormatVisitor);

        return new FileWriter($tableVisitor);
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
        $subset = self::getFontSubset($font, 'g');

        // act
        $output = $writer->writeFile($subset);

        // assert
        $this->assertStringContainsString($subset->getCharacters()[0]->getGlyfTable()->getContent(), $output);
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
        $optimizer = new Optimizer();
        $subset = $optimizer->getFontSubset($font, [$character, $character1, $character2]);

        // act
        $output = $writer->writeFile($subset);

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
        $parser = ParserTest::getParser();
        $font = $parser->parse(FileReaderTest::getDefaultFontContent());
        $writer = self::getFileWriter();
        $subset = $this->getFontSubset($font, 'g');

        // act
        $output = $writer->writeFile($subset);

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
        $subset = $this->getFontSubset($font, 'a');

        // act
        $output = $writer->writeFile($subset);
        $font = $parser->parse($output);

        $subset2 = $this->getFontSubset($font, 'g');
        $output2 = $writer->writeFile($subset2);
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
        $subset = self::getFontSubset($font, 'g');

        // act
        $output = $writer->writeFile($subset);
        $font2 = $parser->parse($output);

        // assert
        $this->assertEquals($font->getFontFile()->getNameTable(), $font2->getFontFile()->getNameTable());
        $this->assertEquals($font->getFontFile()->getPrepTable(), $font2->getFontFile()->getPrepTable());
    }

    /**
     * @param Font $font
     * @param string $character
     *
     * @return Font
     */
    private static function getFontSubset(Font $font, string $character): Font
    {
        $characterRepository = new CharacterRepository($font);
        $character = $characterRepository->find($character);

        $optimizer = new Optimizer();

        return $optimizer->getFontSubset($font, [$character]);
    }
}
