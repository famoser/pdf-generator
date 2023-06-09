<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\File\Object;

use PdfGenerator\Backend\File\Object\Base\BaseObject;
use PdfGenerator\Backend\File\ObjectVisitor;
use PdfGenerator\Backend\File\Token\DictionaryToken;

class StreamObject extends BaseObject
{
    private string $content;

    private DictionaryToken $dictionary;

    /**
     * StreamObject constructor.
     */
    public function __construct(int $number, string $content)
    {
        parent::__construct($number);

        $this->dictionary = new DictionaryToken();
        $this->content = $content;

        $this->dictionary->setNumberEntry('Length', \strlen($this->content));
    }

    public function accept(ObjectVisitor $visitor): string
    {
        return $visitor->visitStream($this);
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getMetaData(): DictionaryToken
    {
        return $this->dictionary;
    }
}
