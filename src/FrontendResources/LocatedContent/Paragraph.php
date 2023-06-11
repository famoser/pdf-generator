<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\FrontendResources\LocatedContent;

use PdfGenerator\FrontendResources\LocatedContent\Base\LocatedContent;
use PdfGenerator\FrontendResources\LocatedContent\Paragraph\Line;
use PdfGenerator\FrontendResources\Position;
use PdfGenerator\FrontendResources\Size;

class Paragraph extends LocatedContent
{
    /**
     * @param Line[] $lines
     */
    public function __construct(Position $position, Size $size, private readonly array $lines)
    {
        parent::__construct($position, $size);
    }

    /**
     * @return Line[]
     */
    public function getLines(): array
    {
        return $this->lines;
    }
}
