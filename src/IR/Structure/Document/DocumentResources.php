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
use PdfGenerator\IR\ContentVisitor;
use PdfGenerator\IR\Structure\Base\BaseContentStructure;
use PdfGenerator\IR\Structure\Base\BaseStructure2;

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
     * @var ContentVisitor
     */
    private $documentContentVisitor;

    /**
     * DocumentResources constructor.
     *
     * @param ContentVisitor $documentContentVisitor
     */
    public function __construct(ContentVisitor $documentContentVisitor)
    {
        $this->documentContentVisitor = $documentContentVisitor;
    }

    /**
     * @param \PdfGenerator\IR\Structure\Font $structure
     *
     * @return Font
     */
    public function getFont(\PdfGenerator\IR\Structure\Font $structure)
    {
        return $this->getOrCreate($structure, $this->fontCache);
    }

    /**
     * @param \PdfGenerator\IR\Structure\Image $structure
     *
     * @return Image
     */
    public function getImage(\PdfGenerator\IR\Structure\Image $structure)
    {
        return $this->getOrCreate($structure, $this->imageCache);
    }

    /**
     * @param BaseContentStructure $structure
     * @param BaseStructure2[] $cache
     *
     * @return BaseStructure2|mixed
     */
    private function getOrCreate($structure, array $cache)
    {
        $identifier = $structure->getIdentifier();

        if (!\array_key_exists($identifier, $cache)) {
            $font = $structure->accept($this->documentContentVisitor);

            $cache[$identifier] = $font;
        }

        return $cache[$identifier];
    }
}
