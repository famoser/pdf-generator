<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Content;

use PdfGenerator\Frontend\Content\Base\Content;
use PdfGenerator\Frontend\Content\Style\ImageStyle;
use PdfGenerator\Frontend\ContentVisitor;
use PdfGenerator\Frontend\MeasuredContent\Base\MeasuredContent;

class Image extends Content
{
    /**
     * @var string
     */
    private $src;

    /**
     * @var ImageStyle
     */
    private $style;

    public function __construct(string $src, ImageStyle $style = null)
    {
        $this->src = $src;
        $this->style = $style ?? new ImageStyle();
    }

    public function getSrc(): string
    {
        return $this->src;
    }

    public function accept(ContentVisitor $contentVisitor): MeasuredContent
    {
        return $contentVisitor->visitImage($this);
    }

    public function getStyle(): ImageStyle
    {
        return $this->style;
    }
}
