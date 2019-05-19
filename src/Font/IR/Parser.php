<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\IR;

use PdfGenerator\Font\Frontend\Content\Font;
use PdfGenerator\Font\Frontend\ContentReader;
use PdfGenerator\Font\Frontend\FileReader;
use PdfGenerator\Font\Frontend\Structure\CMapFormatReader;
use PdfGenerator\Font\Frontend\StructureReader;

class Parser
{
    /**
     * @param string $content
     *
     * @throws \Exception
     *
     * @return Font
     */
    public function parse(string $content): Font
    {
        $fileReader = new FileReader($content);
        $structureReader = new StructureReader();
        $cmapFormatReader = new CMapFormatReader();
        $contentReader = new ContentReader($cmapFormatReader, $structureReader);

        return $contentReader->readFont($fileReader);
    }
}
