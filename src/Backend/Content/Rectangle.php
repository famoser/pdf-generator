<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Content;

use PdfGenerator\Backend\Content\Base\BaseContent;
use PdfGenerator\Backend\Content\Operators\Level\PageLevel;
use PdfGenerator\Backend\ContentVisitor;
use PdfGenerator\Backend\File\File;
use PdfGenerator\Backend\File\Object\Base\BaseObject;
use PdfGenerator\Backend\Structure\Page;

class Rectangle extends BaseContent
{
    const PAINTING_MODE_STROKE = 1;
    const PAINTING_MODE_FILL = 2;
    const PAINTING_MODE_STROKE_FILL = 3;

    /**
     * @var float
     */
    private $width;

    /**
     * @var float
     */
    private $height;

    /**
     * @var int
     */
    private $paintingMode;

    /**
     * @var PageLevel
     */
    private $pageLevel;

    /**
     * @param float $width
     * @param float $height
     * @param int $paintingMode
     * @param PageLevel $pageLevel
     */
    public function __construct(float $width, float $height, int $paintingMode, PageLevel $pageLevel)
    {
        $this->width = $width;
        $this->height = $height;
        $this->paintingMode = $paintingMode;
        $this->pageLevel = $pageLevel;
    }

    /**
     * @return float
     */
    public function getWidth(): float
    {
        return $this->width;
    }

    /**
     * @return float
     */
    public function getHeight(): float
    {
        return $this->height;
    }

    /**
     * @return int
     */
    public function getPaintingMode(): int
    {
        return $this->paintingMode;
    }

    /**
     * @return PageLevel
     */
    public function getPageLevel(): PageLevel
    {
        return $this->pageLevel;
    }

    /**
     * @param ContentVisitor $visitor
     * @param File $file
     * @param Page $page
     *
     * @return BaseObject
     */
    public function accept(ContentVisitor $visitor, File $file, Page $page): BaseObject
    {
        return $visitor->visitRectangle($this, $file, $page);
    }
}
