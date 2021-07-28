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

class ParagraphStyle
{
    public const ALIGNMENT_LEFT = 'ALIGNMENT_LEFT';

    /**
     * @var string
     */
    private $alignment;

    /**
     * @var float
     */
    private $indent;

    /**
     * @var float
     */
    private $marginTop;

    /**
     * @var float
     */
    private $marginBottom;

    /**
     * ParagraphStyle constructor.
     */
    public function __construct(string $alignment = self::ALIGNMENT_LEFT, float $indent = 0, float $marginTop = 0, float $marginBottom = 0)
    {
        $this->alignment = $alignment;
        $this->indent = $indent;
        $this->marginTop = $marginTop;
        $this->marginBottom = $marginBottom;
    }

    public function getAlignment(): string
    {
        return $this->alignment;
    }

    public function getIndent(): float
    {
        return $this->indent;
    }

    public function getMarginTop(): float
    {
        return $this->marginTop;
    }

    public function getMarginBottom(): float
    {
        return $this->marginBottom;
    }
}
