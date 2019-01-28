<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Tests\Pdf\Mock;


use PdfGenerator\Pdf\LayoutFactoryConfigurationInterface;

class LayoutFactoryConfigurationMock implements LayoutFactoryConfigurationInterface
{
    /**
     * @return float
     */
    public function getContentXSize(): float
    {
        return 50;
    }

    /**
     * @return float
     */
    public function getColumnGutter(): float
    {
        return 2;
    }

    /**
     * @return float
     */
    public function getTableColumnGutter(): float
    {
        return 2;
    }
}
