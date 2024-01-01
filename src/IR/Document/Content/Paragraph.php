<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Document\Content;

use PdfGenerator\IR\Document\Content\Base\BaseContent;
use PdfGenerator\IR\Document\Content\Common\Position;
use PdfGenerator\IR\Document\Content\Text\Phrase;

readonly class Paragraph extends BaseContent
{
    /**
     * @param Phrase[] $phrase
     */
    public function __construct(private array $phrase, private Position $position)
    {
    }

    /**
     * @return Phrase[]
     */
    public function getPhrase(): array
    {
        return $this->phrase;
    }

    public function getPosition(): Position
    {
        return $this->position;
    }

    public function accept(ContentVisitorInterface $visitor)
    {
        return $visitor->visitParagraph($this);
    }
}
