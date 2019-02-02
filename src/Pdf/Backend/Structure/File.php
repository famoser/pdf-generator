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
     * @param BaseObject $baseObject
     */
    public function addObject(BaseObject $baseObject)
    {
        $this->body[] = $baseObject;
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
}
