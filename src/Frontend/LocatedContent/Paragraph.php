<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\LocatedContent;

use PdfGenerator\Frontend\LocatedContent\Base\LocatedContent;
use PdfGenerator\Frontend\LocatedContent\Paragraph\Line;
use PdfGenerator\Frontend\Position;
use PdfGenerator\Frontend\Size;

class Paragraph extends LocatedContent
{
    /**
     * Paragraph constructor.
     *
     * @param Line[] $lines
     */
    public function __construct(Position $position, Size $size, private array $lines)
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
