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

use PdfGenerator\Backend\File\Object\Base\BaseObject;
use PdfGenerator\Backend\File\Structure\CrossReferenceTable;
use PdfGenerator\Backend\File\Structure\FileHeader;
use PdfGenerator\Backend\File\Structure\FileTrailer;
use PdfGenerator\Backend\File\Token\DictionaryToken;
use PdfGenerator\Backend\File\Token\NumberToken;
use PdfGenerator\Backend\File\Token\ReferenceToken;

class StructureVisitor
{
    /**
     * @var ObjectVisitor
     */
    private $objectVisitor;

    /**
     * @var TokenVisitor
     */
    private $tokenVisitor;

    /**
     * StructureVisitor constructor.
     */
    public function __construct()
    {
        $this->objectVisitor = new ObjectVisitor();
        $this->tokenVisitor = new TokenVisitor();
    }

    /**
     * @param FileHeader $header
     * @param BaseObject[] $content
     * @param BaseObject $root
     *
     * @return string
     */
    public function render(FileHeader $header, array $content, BaseObject $root)
    {
        $output = $header->accept($this) . "\n";

        $crossReferenceTable = new CrossReferenceTable();
        $crossReferenceTable->registerEntrySize(\strlen($output));

        foreach ($content as $baseObject) {
            $objectContent = $baseObject->accept($this->objectVisitor) . "\n";
            $crossReferenceTable->registerEntrySize(\strlen($objectContent));
            $output .= $objectContent;
        }

        $output .= $crossReferenceTable->accept($this) . "\n";

        $trailer = new FileTrailer(\count($crossReferenceTable->getEntries()), $crossReferenceTable->getLastEntry(), $root);
        $output .= $trailer->accept($this);

        return $output;
    }

    /**
     * @param Structure\FileHeader $param
     *
     * @return string
     */
    public function visitFileHeader(Structure\FileHeader $param)
    {
        return '%PDF-' . $param->getVersion();
    }

    /**
     * @param FileTrailer $param
     *
     * @return string
     */
    public function visitFileTrailer(Structure\FileTrailer $param)
    {
        $trailerDictionary = new DictionaryToken();
        $trailerDictionary->setEntry('Size', new NumberToken($param->getSize() + 1));
        $trailerDictionary->setEntry('Root', new ReferenceToken($param->getRoot()));

        $lines = [];
        $lines[] = 'trailer';
        $lines[] = $trailerDictionary->accept($this->tokenVisitor);
        $lines[] = 'startxref';
        $lines[] = $param->getStartOfCrossReferenceTable();
        $lines[] = '%%EOF';

        return implode("\n", $lines);
    }

    /**
     * @param CrossReferenceTable $param
     *
     * @return string
     */
    public function visitCrossReferenceTable(CrossReferenceTable $param)
    {
        $lines = [];
        $lines[] = 'xref';
        $lines[] = '0 ' . (\count($param->getEntries()) + 1);
        $lines[] = '0000000000 65535 f';

        foreach ($param->getEntries() as $entry) {
            $lines[] = str_pad($entry, 10, '' . STR_PAD_LEFT) . ' 00000 n';
        }

        return implode("\n", $lines);
    }
}
