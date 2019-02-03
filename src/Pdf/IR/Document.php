<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdf\IR;

use Pdf\Backend\File;
use Pdf\IR\Structure\Builder\ContentsBuilder;
use Pdf\IR\Structure\Builder\PageBuilder;
use Pdf\IR\Structure\Builder\ResourcesBuilder;
use Pdf\IR\Structure\Catalog;

class Document
{
    /**
     * @var Catalog
     */
    private $catalog;

    /**
     * @var ResourcesBuilder
     */
    private $resourcesBuilder;

    /**
     * @var PageBuilder[]
     */
    private $pageBuilders = [];

    /**
     * Document constructor.
     */
    public function __construct()
    {
        $this->catalog = new Catalog();
        $this->resourcesBuilder = new ResourcesBuilder();
    }

    /**
     * @return PageBuilder
     */
    public function addPage()
    {
        $pageBuilder = new PageBuilder($this->catalog->getPages(), $this->resourcesBuilder, new ContentsBuilder());
        $this->pageBuilders[] = $pageBuilder;

        return $pageBuilder;
    }

    /**
     * @return ResourcesBuilder
     */
    public function getResourcesBuilder(): ResourcesBuilder
    {
        return $this->resourcesBuilder;
    }

    /**
     * @throws \Exception
     */
    private function buildPages()
    {
        foreach ($this->pageBuilders as $pageBuilder) {
            $this->catalog->getPages()->addPage($pageBuilder->build());
        }
    }

    /**
     * @throws \Exception
     *
     * @return string
     */
    public function render(): string
    {
        $this->buildPages();

        $structureVisitor = new StructureVisitor();
        $file = new File();

        $catalog = $structureVisitor->visitCatalog($this->catalog, $file);

        return $file->render($catalog);
    }
}
