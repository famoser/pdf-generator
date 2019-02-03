<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Supporting;

use PdfGenerator\Backend\Structure\Font;
use PdfGenerator\Backend\Structure\Resources;

class FontCollection
{
    /**
     * @var Resources
     */
    private $resources;

    /**
     * @var Font[]
     */
    private $fontCache = [];

    /**
     * FontCollection constructor.
     *
     * @param Resources $resources
     */
    public function __construct(Resources $resources)
    {
        $this->resources = $resources;
    }

    /**
     * @return Font
     */
    public function getHelvetica()
    {
        return $this->getOrCreateFont(Font::SUBTYPE_TYPE1, Font::BASE_FONT_HELVETICA);
    }

    /**
     * @param string $subtype
     * @param string $baseFont
     *
     * @return Font
     */
    private function getOrCreateFont(string $subtype, string $baseFont)
    {
        $cacheKey = $subtype . '_' . $baseFont;
        if (!isset($this->fontCache[$cacheKey])) {
            $this->fontCache[$cacheKey] = $this->resources->addFont($subtype, $baseFont);
        }

        return $this->fontCache[$cacheKey];
    }
}
