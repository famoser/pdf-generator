<?php

namespace Famoser\PdfGenerator\Backend\File\Object;

use Famoser\PdfGenerator\Backend\File\Object\Base\BaseObject;
use Famoser\PdfGenerator\Backend\File\ObjectVisitor;
use Famoser\PdfGenerator\Backend\File\Token\DictionaryToken;

class StreamObject extends BaseObject
{
    private readonly DictionaryToken $dictionary;

    public function __construct(int $number, private readonly string $content)
    {
        parent::__construct($number);

        $this->dictionary = new DictionaryToken();

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
