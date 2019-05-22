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
        $this->assertContains($character->getGlyfTable()->getContent(), $output);
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
}
