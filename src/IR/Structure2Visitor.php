<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR;

use PdfGenerator\Backend\Structure\Catalog;
use PdfGenerator\Backend\Structure\Contents;
use PdfGenerator\Backend\Structure\Page;
use PdfGenerator\Backend\Structure\Pages;
use PdfGenerator\Backend\Structure\Resources;
use PdfGenerator\IR\Structure2\Content\ContentVisitor;
use PdfGenerator\IR\Transformation\DocumentResources;
use PdfGenerator\IR\Transformation\PageResources;

class Structure2Visitor
{
    /**
     * @var IdentifierGenerator
     */
    private $identifierGenerator;

    /**
     * @var DocumentResources
     */
    private $documentResources;

    /**
     * Structure2Visitor constructor.
     *
     * @param IdentifierGenerator $identifierGenerator
     * @param DocumentResources $documentResources
     */
    public function __construct(IdentifierGenerator $identifierGenerator, DocumentResources $documentResources)
    {
        $this->identifierGenerator = $identifierGenerator;
        $this->documentResources = $documentResources;
    }

    /**
     * @param Structure2\Document $param
     *
     * @return Catalog
     */
    public function visitDocument(Structure2\Document $param)
    {
        $pages = new Pages();
        foreach ($param->getPages() as $page) {
            $page = $this->visitPage($page, new Pages());
            $pages->addPage($page);
        }

        return new Catalog([$pages]);
    }

    /**
     * @param Structure2\Page $param
     * @param Pages $pages
     *
     * @return Page
     */
    public function visitPage(Structure2\Page $param, Pages $pages)
    {
        $mediaBox = [0, 0, 210, 297];

        $pageResources = new PageResources($this->documentResources);
        $contentVisitor = new ContentVisitor($pageResources);
        $contentArray = [];
        foreach ($param->getContent() as $item) {
            $content = $item->accept($contentVisitor);
            $contentArray[] = $content;
        }

        $contents = new Contents($contentArray);
        $resources = new Resources($pageResources->getFonts(), $pageResources->getImages());

        return new Page($pages, $mediaBox, $resources, $contents);
    }
}
