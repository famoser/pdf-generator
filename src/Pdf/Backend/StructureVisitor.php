<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdf\Backend;

use Pdf\Backend\Structure\CrossReferenceTable;
use Pdf\Backend\Structure\FileTrailer;
use Pdf\Backend\Token\DictionaryToken;
use Pdf\Backend\Token\NumberToken;
use Pdf\Backend\Token\ReferenceToken;

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
     * @param Structure\FileHeader $param
     *
     * @return string
     */
    public function visitFileHeader(Structure\FileHeader $param)
    {
        return '%PDF-' . $param->getVersion();
    }

    /**
     * @param Structure\File $param
     *
     * @return string
     */
    public function visitFile(Structure\File $param)
    {
        $content = $param->getHeader()->accept($this) . "\n";

        $crossReferenceTable = new CrossReferenceTable();
        $crossReferenceTable->registerEntrySize(\mb_strlen($content));

        foreach ($param->getBody() as $baseObject) {
            $objectContent = $baseObject->accept($this->objectVisitor) . "\n";
            $crossReferenceTable->registerEntrySize(\mb_strlen($objectContent));
            $content .= $objectContent;
        }

        $content .= $crossReferenceTable->accept($this) . "\n";

        $trailer = new FileTrailer(\count($crossReferenceTable->getEntries()), $crossReferenceTable->getLastEntry(), $param->getRoot());
        $content .= $trailer->accept($this);

        return $content;
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
        $lines[] = 'trailer ' . $trailerDictionary->accept($this->tokenVisitor);
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
            $lines[] = str_pad($entry, 10, '' . STR_PAD_LEFT);
        }

        return implode("\n", $lines);
    }
}
