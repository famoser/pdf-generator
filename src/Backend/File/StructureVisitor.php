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

use PdfGenerator\Backend\File\Structure\CrossReferenceTable;
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
     * @var string[]
     */
    private $bodyEntrySizes = [];

    /**
     * StructureVisitor constructor.
     */
    public function __construct()
    {
        $this->objectVisitor = new ObjectVisitor();
        $this->tokenVisitor = new TokenVisitor();
    }

    /**
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

    /**
     * @return string
     */
    public function visitBody(Structure\Body $param)
    {
        $output = '';

        foreach ($param->getEntries() as $baseObject) {
            $objectContent = $baseObject->accept($this->objectVisitor) . "\n";

            $this->bodyEntrySizes[] = \strlen($objectContent);

            $output .= $objectContent;
        }

        return $output;
    }

    /**
     * @return string[]
     */
    public function getBodyEntrySizes(): array
    {
        return $this->bodyEntrySizes;
    }
}
