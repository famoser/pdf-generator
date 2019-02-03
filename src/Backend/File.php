<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend;

use PdfGenerator\Backend\Object\Base\BaseObject;
use PdfGenerator\Backend\Object\DictionaryObject;
use PdfGenerator\Backend\Object\StreamObject;
use PdfGenerator\Backend\Structure\FileHeader;

class File
{
    /**
     * @var BaseObject[]
     */
    private $body = [];

    /**
     * @var int
     */
    private $bodyCounter = 1;

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
     * @param BaseObject $baseObject
     */
    private function addObject(BaseObject $baseObject)
    {
        $this->body[] = $baseObject;
    }

    /**
     * @param BaseObject $root
     *
     * @return string
     */
    public function render(BaseObject $root): string
    {
        $structureVisitor = new StructureVisitor();

        return $structureVisitor->render(new FileHeader(), $this->body, $root);
    }
}
