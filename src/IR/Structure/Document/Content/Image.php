<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure;

use PdfGenerator\IR\ContentVisitor;
use PdfGenerator\IR\Structure\Base\BaseContentStructure;

class Image extends BaseContentStructure
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
     * @param ContentVisitor $visitor
     *
     * @return mixed
     */
    public function accept(ContentVisitor $visitor)
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
