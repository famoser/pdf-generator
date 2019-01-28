<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Pdf;

use PdfGenerator\Layout\AutoColumnLayoutInterface;
use PdfGenerator\Layout\ColumnLayoutInterface;
use PdfGenerator\Layout\Configuration\ColumnConfiguration;
use PdfGenerator\Layout\FullWidthLayoutInterface;
use PdfGenerator\Layout\GroupLayoutInterface;
use PdfGenerator\Layout\TableLayoutInterface;
use PdfGenerator\LayoutFactoryInterface;
use PdfGenerator\Pdf\Layout\AutoColumnLayout;
use PdfGenerator\Pdf\Layout\ColumnLayout;
use PdfGenerator\Pdf\Layout\FullWidthLayout;
use PdfGenerator\Pdf\Layout\GroupLayout;
use PdfGenerator\Pdf\Layout\TableLayout;

class LayoutFactory implements LayoutFactoryInterface
{
    /**
     * @var PdfDocumentInterface
     */
    private $document;

    /**
     * @var LayoutFactoryConfigurationInterface
     */
    private $layoutService;

    /**
     * Document constructor.
     *
     * @param PdfDocumentInterface $pdfDocument
     * @param LayoutFactoryConfigurationInterface $layoutService
     */
    public function __construct(PdfDocumentInterface $pdfDocument, LayoutFactoryConfigurationInterface $layoutService)
    {
        $this->document = $pdfDocument;
        $this->layoutService = $layoutService;
    }

    /**
     * will avoid a page break between the next printed elements
     * will add a page break before all elements if they do not fit on the same page
     * active until end region is called.
     *
     * @return GroupLayoutInterface
     */
    public function createGroupLayout()
    {
        return new GroupLayout($this->document, $this->layoutService->getContentXSize());
    }

    /**
     * starts a region with columns.
     *
     * @param int $columnCount
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
     * starts a region with 100% width.
     *
     * @return FullWidthLayoutInterface
     */
    public function createFullWidthLayout()
    {
        return new FullWidthLayout($this->document, $this->layoutService->getContentXSize());
    }

    /**
     * starts a region with columns and the column is chosen automatically.
     *
     * @param int $columnCount
     *
     * @return AutoColumnLayoutInterface
     */
    public function createAutoColumnLayout(int $columnCount)
    {
        return new AutoColumnLayout($this->document, $columnCount, $this->layoutService->getColumnGutter(), $this->layoutService->getContentXSize());
    }
}
