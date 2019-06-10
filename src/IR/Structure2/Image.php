<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure2;

use PdfGenerator\IR\Structure2\Base\BaseStructure2;
use PdfGenerator\IR\Structure2Visitor;

class Image extends BaseStructure2
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
     * @param Structure2Visitor $visitor
     *
     * @return mixed
     */
    public function accept(Structure2Visitor $visitor)
    {
        return $visitor->visitImage($this);
    }

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->imagePath;
    }
}
