<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdf\Backend\Structure;

use Pdf\Backend\Object\Base\BaseObject;
use Pdf\Backend\Object\DictionaryObject;
use Pdf\Backend\Object\StreamObject;
use Pdf\Backend\Structure\Base\BaseStructure;
use Pdf\Backend\StructureVisitor;

class File extends BaseStructure
{
    /**
     * @var FileHeader
     */
    private $header;

    /**
     * @var BaseObject[]
     */
    private $body = [];

    /**
     * @var int
     */
    private $bodyCounter = 1;

    /**
     * File constructor.
     *
     * @param BaseObject $root
     */
    public function __construct(BaseObject $root)
    {
        $this->header = new FileHeader();

        $this->addObject($root);
    }

    /**
     * @param string $content
     *
     * @return StreamObject
     */
    public function addStreamObject(string $content)
    {
        $streamObject = new StreamObject($this->bodyCounter++, $content);
        $this->addObject($streamObject);

        return $streamObject;
    }

    /**
     * @return DictionaryObject
     */
    public function addDictionaryObject()
    {
        $dictionaryObject = new DictionaryObject($this->bodyCounter++);
        $this->addObject($dictionaryObject);

        return $dictionaryObject;
    }

    /**
     * @param StructureVisitor $visitor
     *
     * @return string
     */
    public function accept(StructureVisitor $visitor): string
    {
        return $visitor->visitFile($this);
    }

    /**
     * @return FileHeader
     */
    public function getHeader(): FileHeader
    {
        return $this->header;
    }

    /**
     * @return BaseObject[]
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * @return BaseObject
     */
    public function getRoot(): BaseObject
    {
        return $this->body[0];
    }

    /**
     * @param BaseObject $baseObject
     */
    private function addObject(BaseObject $baseObject)
    {
        $this->body[] = $baseObject;
    }
}
