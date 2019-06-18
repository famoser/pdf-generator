<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Document;

use PdfGenerator\IR\Structure\Document\Base\BaseDocumentStructure;
use PdfGenerator\IR\Structure\DocumentVisitor;

class Image extends BaseDocumentStructure
{
    /**
     * @var string
     */
    private $imagePath;

    /**
     * Image constructor.
     *
     * @param string $imagePath
     */
    public function __construct(string $imagePath)
    {
        $this->imagePath = $imagePath;
    }

    /**
     * @param DocumentVisitor $visitor
     *
     * @return mixed
     */
    public function accept(DocumentVisitor $visitor)
    {
        return $visitor->visitImage($this);
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->imagePath;
    }

    /**
     * @return string
     */
    public function getImagePath(): string
    {
        return $this->imagePath;
    }
}
