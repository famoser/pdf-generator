<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\IR\Document\Content;

use Famoser\PdfGenerator\IR\Document\Content\Base\BaseContent;
use Famoser\PdfGenerator\IR\Document\Content\Common\Position;
use Famoser\PdfGenerator\IR\Document\Content\Text\TextLine;

readonly class Text extends BaseContent
{
    /**
     * @param TextLine[] $lines
     */
    public function __construct(private array $lines, private Position $position)
    {
    }

    /**
     * @return TextLine[]
     */
    public function getLines(): array
    {
        return $this->lines;
    }

    public function getPosition(): Position
    {
        return $this->position;
    }

    public function accept(ContentVisitorInterface $visitor)
    {
        return $visitor->visitText($this);
    }
}
