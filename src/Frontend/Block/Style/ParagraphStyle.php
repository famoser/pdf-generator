<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Block\Style;

use PdfGenerator\Frontend\Block\Style\Base\BlockStyle;

class ParagraphStyle extends BlockStyle
{
    public const ALIGNMENT_LEFT = 'ALIGNMENT_LEFT';

    /**
     * @var string
     */
    private $alignment;

    /**
     * ParagraphStyle constructor.
     */
    public function __construct(string $alignment = self::ALIGNMENT_LEFT)
    {
        parent::__construct();

        $this->alignment = $alignment;
    }

    public function getAlignment(): string
    {
        return $this->alignment;
    }
}
