<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Backend\File\Table;

use PdfGenerator\Font\Backend\File\Table\Base\BaseTable;
use PdfGenerator\Font\Backend\File\TableVisitor;
use PdfGenerator\Font\Backend\File\Traits\BoundingBoxTrait;
use PdfGenerator\Font\Backend\File\Traits\RawContent;

/**
 * the glyph table specified the appearance of the glyphs
 * needs numGlyphs from maxp table (to know how many glyphs are there)
 * needs offsets of glyphys from loca table (to know which glyph corresponds to which glyph id).
 *
 * @see https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6glyf.html
 * @see https://docs.microsoft.com/en-us/typography/opentype/spec/glyf
 *
 * each entry contains its bounding box and the actual glyph data
 */
class GlyfTable extends BaseTable
{
    /**
     * number of contours
     * if >=0 then simple glyph
     * else composite graph.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $numberOfContours;

    use BoundingBoxTrait;

    /*
     * the raw glyph data
     */
    use RawContent;

    /**
     * @return int
     */
    public function getNumberOfContours(): int
    {
        return $this->numberOfContours;
    }

    /**
     * @param int $numberOfContours
     */
    public function setNumberOfContours(int $numberOfContours): void
    {
        $this->numberOfContours = $numberOfContours;
    }

    /**
     * @param TableVisitor $visitor
     *
     * @return string
     */
    public function accept(TableVisitor $visitor): string
    {
        return $visitor->visitGlyfTable($this);
    }
}
