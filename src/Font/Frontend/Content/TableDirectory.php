<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend\Content;

class TableDirectory
{
    /**
     * @var CMapFormatDirectory
     */
    private $cmapFormatDirectory;

    /**
     * @return CMapFormatDirectory
     */
    public function getCmapFormatDirectory(): CMapFormatDirectory
    {
        return $this->cmapFormatDirectory;
    }

    /**
     * @param CMapFormatDirectory $cmapFormatDirectory
     */
    public function setCmapFormatDirectory(CMapFormatDirectory $cmapFormatDirectory): void
    {
        $this->cmapFormatDirectory = $cmapFormatDirectory;
    }
}
