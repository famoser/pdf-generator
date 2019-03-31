<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Content;

use PdfGenerator\Backend\Document;
use PdfGenerator\Backend\Structure\Builder\PageBuilder;

class PageRepository
{
    /**
     * @var Document
     */
    private $document;

    /**
     * @var PageBuilder[]
     */
    private $pageBuilders = [];

    /**
     * FontRepository constructor.
     *
     * @param Document $document
     */
    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    /**
     * @param int $pageNumber
     *
     * @return PageBuilder
     */
    public function getPage(int $pageNumber)
    {
        return $this->getOrCreatePageBuilder($pageNumber);
    }

    /**
     * @param int $page
     *
     * @return PageBuilder
     */
    protected function getOrCreatePageBuilder(int $page)
    {
        $pageBuildersCount = \count($this->pageBuilders);
        $targetPageBuilderIndex = $page - 1;

        while ($targetPageBuilderIndex >= $pageBuildersCount) {
            $pageBuilder = $this->document->addPage();
            $pageBuilder->setMediaBox(210, 297);

            $this->pageBuilders[] = $pageBuilder;
            ++$pageBuildersCount;
        }

        return $this->pageBuilders[$targetPageBuilderIndex];
    }
}
