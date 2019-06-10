<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Transformation;

use PdfGenerator\Backend\Structure\Font;
use PdfGenerator\Backend\Structure\Image;
use PdfGenerator\IR\Structure2\Base\BaseStructure2;
use PdfGenerator\IR\Structure2Visitor;

class DocumentResources
{
    /**
     * @var Font[]
     */
    private $fontCache = [];

    /**
     * @var Image[]
     */
    private $imageCache = [];

    /**
     * @var Structure2Visitor
     */
    private $structure2Visitor;

    /**
     * FontRepository constructor.
     *
     * @param Structure2Visitor $structure2Visitor
     */
    public function __construct(Structure2Visitor $structure2Visitor)
    {
        $this->structure2Visitor = $structure2Visitor;
    }

    /**
     * @param \PdfGenerator\IR\Structure2\Font $structure
     *
     * @return Font
     */
    public function getFont(\PdfGenerator\IR\Structure2\Font $structure)
    {
        return $this->getOrCreate($structure, $this->fontCache);
    }

    /**
     * @param \PdfGenerator\IR\Structure2\Image $structure
     *
     * @return Image
     */
    public function getImage(\PdfGenerator\IR\Structure2\Image $structure)
    {
        return $this->getOrCreate($structure, $this->imageCache);
    }

    /**
     * @param BaseStructure2 $structure
     * @param BaseStructure2[] $cache
     *
     * @return BaseStructure2|mixed
     */
    private function getOrCreate(BaseStructure2 $structure, array $cache)
    {
        $identifier = $structure->getIdentifier();

        if (!\array_key_exists($identifier, $cache)) {
            $font = $structure->accept($this->structure2Visitor);

            $cache[$identifier] = $font;
        }

        return $cache[$identifier];
    }
}
