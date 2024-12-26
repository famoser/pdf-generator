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

use Famoser\PdfGenerator\Backend\File\Object\Base\BaseObject;
use Famoser\PdfGenerator\Backend\File\Object\DictionaryObject;
use Famoser\PdfGenerator\Backend\File\Object\StreamObject;

class ObjectVisitor
{
    private readonly TokenVisitor $tokenVisitor;

    public function __construct()
    {
        $this->tokenVisitor = new TokenVisitor();
    }

    public function visitDictionary(DictionaryObject $dictionary): string
    {
        return $this->visitObject($dictionary, $dictionary->getDictionaryToken()->accept($this->tokenVisitor));
    }

    public function visitStream(StreamObject $param): string
    {
        $lines = [];
        $lines[] = $param->getMetaData()->accept($this->tokenVisitor);
        $lines[] = 'stream';
        $lines[] = $param->getContent();
        $lines[] = 'endstream';

        return $this->visitObject($param, implode("\n", $lines));
    }

    private function visitObject(BaseObject $object, string $content): string
    {
        return $object->getNumber()." 0 obj\n".$content."\nendobj";
    }
}
