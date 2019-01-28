<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Report\Document\Pdf;

use App\Service\Report\Document\Layout\AutoColumnLayoutInterface;
use App\Service\Report\Document\Layout\ColumnLayoutInterface;
use App\Service\Report\Document\Layout\Configuration\ColumnConfiguration;
use App\Service\Report\Document\Layout\FullWidthLayoutInterface;
use App\Service\Report\Document\Layout\GroupLayoutInterface;
use App\Service\Report\Document\Layout\TableLayoutInterface;
use App\Service\Report\Document\LayoutFactoryInterface;
use App\Service\Report\Document\Pdf\Layout\AutoColumnLayout;
use App\Service\Report\Document\Pdf\Layout\ColumnLayout;
use App\Service\Report\Document\Pdf\Layout\FullWidthLayout;
use App\Service\Report\Document\Pdf\Layout\GroupLayout;
use App\Service\Report\Document\Pdf\Layout\TableLayout;

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
