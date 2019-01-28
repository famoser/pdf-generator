<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Tests\Pdf;

use PdfGenerator\Layout\AutoColumnLayoutInterface;
use PdfGenerator\Layout\ColumnLayoutInterface;
use PdfGenerator\Layout\Configuration\ColumnConfiguration;
use PdfGenerator\Layout\FullWidthLayoutInterface;
use PdfGenerator\Layout\GroupLayoutInterface;
use PdfGenerator\Layout\TableLayoutInterface;
use PdfGenerator\Pdf\LayoutFactory;
use PdfGenerator\Pdf\LayoutFactoryConfigurationInterface;
use PdfGenerator\Pdf\PdfDocumentInterface;
use PdfGenerator\Tests\Pdf\Mock\LayoutFactoryConfigurationMock;
use PdfGenerator\Tests\Pdf\Mock\PdfDocumentMock;
use PHPUnit\Framework\TestCase;

class LayoutFactoryTest extends TestCase
{
    /**
     * @var PdfDocumentInterface
     */
    private $pdfDocument;

    /**
     * @var LayoutFactoryConfigurationInterface
     */
    private $layoutFactoryConfiguration;

    /**
     * LayoutFactoryTest constructor.
     *
     * @param string|null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->pdfDocument = new PdfDocumentMock();
        $this->layoutFactoryConfiguration = new LayoutFactoryConfigurationMock();
    }

    /**
     * @throws \Exception
     */
    public function testImplementationsReturned()
    {
        $layoutFactory = new LayoutFactory($this->pdfDocument, $this->layoutFactoryConfiguration);

        $this->assertInstanceOf(AutoColumnLayoutInterface::class, $layoutFactory->createAutoColumnLayout(2));
        $this->assertInstanceOf(ColumnLayoutInterface::class, $layoutFactory->createColumnLayout(2));
        $this->assertInstanceOf(FullWidthLayoutInterface::class, $layoutFactory->createFullWidthLayout());
        $this->assertInstanceOf(GroupLayoutInterface::class, $layoutFactory->createGroupLayout());
        $this->assertInstanceOf(TableLayoutInterface::class, $layoutFactory->createTableLayout([new ColumnConfiguration()]));
    }
}
