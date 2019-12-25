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

use DocumentGenerator\Layout\AutoColumnLayoutInterface;
use DocumentGenerator\Layout\ColumnLayoutInterface;
use DocumentGenerator\Layout\Configuration\ColumnConfiguration;
use DocumentGenerator\Layout\TableLayoutInterface;
use DocumentGenerator\LayoutFactoryInterface;
use PdfGenerator\Frontend\Layout\AutoColumnLayout;
use PdfGenerator\Frontend\Layout\ColumnLayout;
use PdfGenerator\Frontend\Layout\TableLayout;

class LayoutFactory implements LayoutFactoryInterface
{
    /**
     * @var Document
     */
    private $document;

    /**
     * @var LayoutFactoryConfigurationInterface
     */
    private $layoutService;

    /**
     * Document constructor.
     */
    public function __construct(Document $pdfDocument, LayoutFactoryConfigurationInterface $layoutService)
    {
        $this->document = $pdfDocument;
        $this->layoutService = $layoutService;
    }

    /**
     * starts a region with columns.
     *
     * @return ColumnLayoutInterface
     */
    public function createColumnLayout(int $columnCount)
    {
        return new ColumnLayout($this->document, $columnCount, $this->layoutService->getColumnGutter(), $this->layoutService->getContentXSize());
    }

    /**
     * starts a table.
     *
     * @param ColumnConfiguration[] $tableColumns
     *
     * @throws \Exception
     *
     * @return TableLayoutInterface
     */
    public function createTableLayout(array $tableColumns)
    {
        return new TableLayout($this->document, $this->layoutService->getContentXSize(), $this->layoutService->getTableColumnGutter(), $tableColumns);
    }

    /**
     * starts a region with columns and the column is chosen automatically.
     *
     * @return AutoColumnLayoutInterface
     */
    public function createAutoColumnLayout(int $columnCount)
    {
        return new AutoColumnLayout($this->document, $columnCount, $this->layoutService->getColumnGutter(), $this->layoutService->getContentXSize());
    }
}
