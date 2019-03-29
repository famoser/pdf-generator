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
    /**
     * @var string
     */
    private $content;

    /**
     * @var DictionaryToken
     */
    private $dictionary;

    /**
     * @var int
     */
    private $contentType;

    public const CONTENT_TYPE_TEXT = 1;
    public const CONTENT_TYPE_IMAGE = 2;

    /**
     * StreamObject constructor.
     *
     * @param int $number
     * @param string $content
     * @param int $contentType
     */
    public function __construct(int $number, string $content, int $contentType)
    {
        parent::__construct($number);

        $this->dictionary = new DictionaryToken();
        $this->content = $content;

        /* should allow to compress, currently does not work and not the target of the project
        if ($contentType === self::CONTENT_TYPE_TEXT && \extension_loaded('zlib')) {
            $this->dictionary->setTextEntry('Filter', '/FlatDecode');
            $this->content = gzcompress($this->content);
        }
        */

        $this->dictionary->setNumberEntry('Length', \mb_strlen($this->content));
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
