<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\File;

use PdfGenerator\Backend\File\Object\DictionaryObject;
use PdfGenerator\Backend\File\Object\StreamObject;
use PdfGenerator\Backend\File\Structure\Body;
use PdfGenerator\Backend\File\Structure\CrossReferenceTable;
use PdfGenerator\Backend\File\Structure\FileHeader;
use PdfGenerator\Backend\File\Structure\FileTrailer;

class File
{
    /**
     * @var Body
     */
    private $body;

    /**
     * @var int
     */
    private $bodyCounter = 1;

    /**
     * File constructor.
     */
    public function __construct()
    {
        $this->body = new Body();
    }

    /**
     * @return StreamObject
     */
    public function addStreamObject(string $content)
    {
        $streamObject = new StreamObject($this->bodyCounter++, $content);
        $this->body->addObject($streamObject);

        return $streamObject;
    }

    /**
     * @return DictionaryObject
     */
    public function addDictionaryObject()
    {
        $dictionaryObject = new DictionaryObject($this->bodyCounter++);
        $this->body->addObject($dictionaryObject);

        return $dictionaryObject;
    }

    /**
     * @return DictionaryObject
     */
    public function addInfoDictionaryObject()
    {
        $dictionaryObject = new DictionaryObject($this->bodyCounter++);
        $this->body->addInfoObject($dictionaryObject);

        return $dictionaryObject;
    }

    public function render(): string
    {
        $structureVisitor = new StructureVisitor();

        $header = new FileHeader();
        $output = $header->accept($structureVisitor) . "\n";
        $headerLength = \strlen($output);

        $output .= $this->body->accept($structureVisitor);

        $crossReferenceTable = new CrossReferenceTable();
        $crossReferenceTable->registerEntrySize($headerLength);
        $crossReferenceTable->registerEntrySizes($structureVisitor->getBodyEntrySizes());
        $output .= $crossReferenceTable->accept($structureVisitor) . "\n";

        $trailer = new FileTrailer(\count($crossReferenceTable->getEntries()), $crossReferenceTable->getLastEntry(), $this->body->getRootEntry(), $this->body->getInfoObject());
        $output .= $trailer->accept($structureVisitor);

        return $output;
    }
}
