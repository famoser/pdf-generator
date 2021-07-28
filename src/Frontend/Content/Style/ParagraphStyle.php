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
    private $marginTop;

    /**
     * @var float
     */
    private $marginBottom;

    /**
     * ParagraphStyle constructor.
     */
    public function __construct(string $alignment = self::ALIGNMENT_LEFT, float $marginTop = 0, float $marginBottom = 0)
    {
        $this->alignment = $alignment;
        $this->marginTop = $marginTop;
        $this->marginBottom = $marginBottom;
    }

    public function getAlignment(): string
    {
        return $this->alignment;
    }

    /**
     * @return float
     */
    public function getMarginTop()
    {
        return $this->marginTop;
    }

    /**
     * @return float
     */
    public function getMarginBottom()
    {
        return $this->marginBottom;
    }
}
