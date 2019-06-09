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
use PdfGenerator\Backend\File\File;
use PdfGenerator\Backend\File\Object\Base\BaseObject;
use PdfGenerator\Backend\Structure\ContentVisitor;
use PdfGenerator\Backend\Structure\Image;
use PdfGenerator\Backend\Structure\Page;

class ImageContent extends BaseContent
{
    /**
     * @var Image
     */
    private $image;

    /**
     * @var PageLevel
     */
    private $pageLevel;

    /**
     * @param Image $image
     * @param PageLevel $pageLevel
     */
    public function __construct(Image $image, PageLevel $pageLevel)
    {
        $this->image = $image;
        $this->pageLevel = $pageLevel;
    }

    /**
     * @return Image
     */
    public function getImage(): Image
    {
        return $this->image;
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
        return $visitor->visitImageContent($this, $file, $page);
    }
}
