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

class RowStyle extends BlockStyle
{
    /**
     * @var float[]|null
     */
    private $columnWidths;

    /**
     * RowStyle constructor.
     *
     * @param float[]|null $columnWidths
     */
    public function __construct(array $columnWidths = null)
    {
        parent::__construct();

        $this->columnWidths = $columnWidths;
    }

    /**
     * @return float[]|null
     */
    public function getColumnWidths(): ?array
    {
        return $this->columnWidths;
    }
}
