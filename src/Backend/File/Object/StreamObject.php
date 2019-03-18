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
use PdfGenerator\Backend\File\Token\NumberToken;

class StreamObject extends BaseObject
{
    /**
     * @var string
     */
    private $content;

    /**
     * @var DictionaryToken
     */
    private $dictionary;

    /**
     * StreamObject constructor.
     *
     * @param int $number
     * @param string $content
     */
    public function __construct(int $number, string $content)
    {
        parent::__construct($number);

        $this->content = $content;

        $this->dictionary = new DictionaryToken();
        $this->dictionary->setEntry('Length', new NumberToken(\strlen($this->content)));
    }

    /**
     * @param ObjectVisitor $visitor
     *
     * @return string
     */
    public function accept(ObjectVisitor $visitor): string
    {
        return $visitor->visitStream($this);
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return DictionaryToken
     */
    public function getMetaData(): DictionaryToken
    {
        return $this->dictionary;
    }
}
