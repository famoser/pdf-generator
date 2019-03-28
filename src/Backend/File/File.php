<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\File;

use PdfGenerator\Backend\File\Object\Base\BaseObject;
use PdfGenerator\Backend\File\Object\DictionaryObject;
use PdfGenerator\Backend\File\Object\StreamObject;
use PdfGenerator\Backend\File\Structure\FileHeader;

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
     * @param int $contentType
     *
     * @return StreamObject
     */
    public function addStreamObject(string $content, int $contentType)
    {
        $streamObject = new StreamObject($this->bodyCounter++, $content, $contentType);
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
