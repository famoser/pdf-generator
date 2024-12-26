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

use Famoser\PdfGenerator\Backend\File\Structure\CrossReferenceTable;
use Famoser\PdfGenerator\Backend\File\Token\DictionaryToken;

class StructureVisitor
{
    private readonly ObjectVisitor $objectVisitor;

    private readonly TokenVisitor $tokenVisitor;

    /**
     * @var int[]
     */
    private array $bodyEntrySizes = [];

    public function __construct()
    {
        $this->objectVisitor = new ObjectVisitor();
        $this->tokenVisitor = new TokenVisitor();
    }

    public function visitFileHeader(Structure\FileHeader $param): string
    {
        return '%PDF-'.$param->getVersion()."\n".
            '%'.hex2bin('E2E3CFD3'); // declares that PDF contains binary data
    }

    public function visitFileTrailer(Structure\FileTrailer $param): string
    {
        $trailerDictionary = new DictionaryToken();
        $trailerDictionary->setNumberEntry('Size', $param->getSize() + 1);
        $trailerDictionary->setReferenceEntry('Root', $param->getRoot());
        $trailerDictionary->setReferenceEntry('Info', $param->getInfo());

        $lines = [];
        $lines[] = 'trailer';
        $lines[] = $trailerDictionary->accept($this->tokenVisitor);
        $lines[] = 'startxref';
        $lines[] = $param->getStartOfCrossReferenceTable();
        $lines[] = '%%EOF';

        return implode("\n", $lines);
    }

    public function visitCrossReferenceTable(CrossReferenceTable $param): string
    {
        $entries = $param->getEntries();

        $lines = [];
        $lines[] = 'xref';
        $lines[] = '0 '.(\count($entries) + 1);
        $lines[] = '0000000000 65535 f';

        foreach ($entries as $entry) {
            $lines[] = str_pad((string) $entry, 10, '0', \STR_PAD_LEFT).' 00000 n';
        }

        return implode("\n", $lines);
    }

    public function visitBody(Structure\Body $param): string
    {
        $output = '';

        foreach ($param->getEntries() as $baseObject) {
            $objectContent = $baseObject->accept($this->objectVisitor)."\n";

            $this->bodyEntrySizes[] = \strlen($objectContent);

            $output .= $objectContent;
        }

        return $output;
    }

    /**
     * @return int[]
     */
    public function getBodyEntrySizes(): array
    {
        return $this->bodyEntrySizes;
    }
}
