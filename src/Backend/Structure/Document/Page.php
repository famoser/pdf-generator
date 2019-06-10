<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure;

use PdfGenerator\Backend\Catalog\Contents;
use PdfGenerator\Backend\Catalog\Pages;
use PdfGenerator\Backend\Catalog\Resources;
use PdfGenerator\Backend\Structure\Base\BaseContent;
use PdfGenerator\Backend\Structure\Document\Page\ContentVisitor;
use PdfGenerator\Backend\Transformation\DocumentResources;
use PdfGenerator\Backend\Transformation\PageResources;

class Page
{
    /**
     * @var BaseContent[]
     */
    private $content = [];

    /**
     * @var int[]
     */
    private $mediaBox;

    /**
     * Page constructor.
     *
     * @param int[] $mediaBox
     */
    public function __construct(array $mediaBox)
    {
        $this->mediaBox = $mediaBox;
    }

    /**
     * @param BaseContent $content
     */
    public function addContent(BaseContent $content)
    {
        $this->content[] = $content;
    }

    /**
     * @param Pages $parent
     * @param DocumentResources $documentResources
     *
     * @return \PdfGenerator\Backend\Catalog\Page
     */
    public function render(Pages $parent, DocumentResources $documentResources)
    {
        $pageResources = new PageResources($documentResources);

        $contentVisitor = new ContentVisitor($pageResources);

        $contentArray = [];
        foreach ($this->content as $item) {
            $content = $item->accept($contentVisitor);
            $contentArray[] = $content;
        }

        $contents = new Contents($contentArray);
        $resources = new Resources($pageResources->getFonts(), $pageResources->getImages());

        return new \PdfGenerator\Backend\Catalog\Page($parent, $this->mediaBox, $resources, $contents);
    }
}
