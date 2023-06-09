<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Content\Style;

use PdfGenerator\Frontend\Content\Style\Base\Style;

class ParagraphStyle extends Style
{
    public const ALIGNMENT_LEFT = 'ALIGNMENT_LEFT';

    /**
     * ParagraphStyle constructor.
     */
    public function __construct(private string $alignment = self::ALIGNMENT_LEFT, private float $indent = 0)
    {
    }

    public function getAlignment(): string
    {
        return $this->alignment;
    }

    public function getIndent(): float
    {
        return $this->indent;
    }
}
