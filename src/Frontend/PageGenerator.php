<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend;

use PdfGenerator\Frontend\Block\Page;
use PdfGenerator\Frontend\Block\Style\PageStyle;

class PageGenerator
{
    /**
     * @var PageStyle
     */
    private $pageStyle;

    /**
     * @var float[]
     */
    private $pageDimensions;

    /**
     * PageGenerator constructor.
     *
     * @param float[] $pageDimensions
     */
    public function __construct(PageStyle $pageStyle = null, array $pageDimensions = null)
    {
        $this->pageStyle = $pageStyle ?? new PageStyle();
        $this->pageDimensions = $pageDimensions ?? [210, 297]; // A4 is default
    }

    public function getNextPage(): Page
    {
        return new Page($this->pageStyle, $this->pageDimensions);
    }
}
