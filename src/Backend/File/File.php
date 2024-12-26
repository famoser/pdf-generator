<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Backend\File;

use Famoser\PdfGenerator\Backend\File\Object\DictionaryObject;
use Famoser\PdfGenerator\Backend\File\Object\StreamObject;
use Famoser\PdfGenerator\Backend\File\Structure\Body;
use Famoser\PdfGenerator\Backend\File\Structure\CrossReferenceTable;
use Famoser\PdfGenerator\Backend\File\Structure\FileHeader;
use Famoser\PdfGenerator\Backend\File\Structure\FileTrailer;

class File
{
    private readonly Body $body;

    private int $bodyCounter = 1;

    public function __construct()
    {
        $this->body = new Body();
    }

    public function addStreamObject(string $content): StreamObject
    {
        $streamObject = new StreamObject($this->bodyCounter++, $content);
        $this->body->addObject($streamObject);

        return $streamObject;
    }

    public function addDictionaryObject(): DictionaryObject
    {
        $dictionaryObject = new DictionaryObject($this->bodyCounter++);
        $this->body->addObject($dictionaryObject);

        return $dictionaryObject;
    }

    public function addInfoDictionaryObject(): DictionaryObject
    {
        $dictionaryObject = new DictionaryObject($this->bodyCounter++);
        $this->body->addInfoObject($dictionaryObject);

        return $dictionaryObject;
    }

    public function render(): string
    {
        $structureVisitor = new StructureVisitor();

        $header = new FileHeader();
        $output = $header->accept($structureVisitor)."\n";
        $headerLength = \strlen($output);

        $output .= $this->body->accept($structureVisitor);

        $crossReferenceTable = new CrossReferenceTable();
        $crossReferenceTable->registerEntrySize($headerLength);
        $crossReferenceTable->registerEntrySizes($structureVisitor->getBodyEntrySizes());
        $output .= $crossReferenceTable->accept($structureVisitor)."\n";

        $trailer = new FileTrailer(\count($crossReferenceTable->getEntries()), $crossReferenceTable->getLastEntry(), $this->body->getRootEntry(), $this->body->getInfoObject());
        $output .= $trailer->accept($structureVisitor);

        return $output;
    }
}
