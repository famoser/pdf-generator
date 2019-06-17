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

use PdfGenerator\Backend\Catalog\Catalog;
use PdfGenerator\Backend\Catalog\Pages;
use PdfGenerator\Backend\Transformation\DocumentResources;
use PdfGenerator\IR\Structure\Optimization\Configuration;

class Document
{
    /**
     * @var Page[]
     */
    private $pages = [];

    /**
     * @param Page $page
     */
    public function addPage(Page $page)
    {
        $this->pages[] = $page;
    }

    /**
     * @return Catalog
     */
    public function render()
    {
        $documentVisitor = new DocumentVisitor(new Configuration());
        $documentResources = new DocumentResources($documentVisitor);

        $pages = new Pages();
        foreach ($this->pages as $page) {
            $renderedPage = $page->render($pages, $documentResources);
            $pages->addPage($renderedPage);
        }

        return new Catalog([$pages]);
    }

    /**
     * @return string
     */
    public function save()
    {
        return $this->render()->save();
    }
}
