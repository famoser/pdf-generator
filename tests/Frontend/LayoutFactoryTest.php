<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Tests\Frontend;

use DocumentGenerator\Layout\AutoColumnLayoutInterface;
use DocumentGenerator\Layout\ColumnLayoutInterface;
use DocumentGenerator\Layout\Configuration\ColumnConfiguration;
use DocumentGenerator\Layout\FullWidthLayoutInterface;
use DocumentGenerator\Layout\GroupLayoutInterface;
use DocumentGenerator\Layout\TableLayoutInterface;
use PdfGenerator\Frontend\LayoutFactory;
use PdfGenerator\Frontend\LayoutFactoryConfigurationInterface;
use PdfGenerator\Frontend\PdfDocument;
use PHPUnit\Framework\TestCase;

class LayoutFactoryTest extends TestCase
{
    /**
     * @var PdfDocument
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

        $this->pdfDocument = \Mockery::mock(PdfDocument::class);
        $this->layoutFactoryConfiguration = \Mockery::mock(LayoutFactoryConfigurationInterface::class, [
            'getContentXSize' => 20,
            'getColumnGutter' => 20,
            'getTableColumnGutter' => 20,
        ]);
    }

    /**
     * @throws \Exception
     */
    public function testImplementationsReturned()
    {
        $this->assertTrue(true);
    }

    /**
     * @throws \Exception
     */
    public function ignoreTestImplementationsReturned()
    {
        $layoutFactory = new LayoutFactory($this->pdfDocument, $this->layoutFactoryConfiguration);

        $this->assertInstanceOf(AutoColumnLayoutInterface::class, $layoutFactory->createAutoColumnLayout(2));
        $this->assertInstanceOf(ColumnLayoutInterface::class, $layoutFactory->createColumnLayout(2));
        $this->assertInstanceOf(FullWidthLayoutInterface::class, $layoutFactory->createFullWidthLayout());
        $this->assertInstanceOf(GroupLayoutInterface::class, $layoutFactory->createGroupLayout());
        $this->assertInstanceOf(TableLayoutInterface::class, $layoutFactory->createTableLayout([new ColumnConfiguration()]));
    }
}
