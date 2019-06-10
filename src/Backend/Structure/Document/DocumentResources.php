<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Transformation;

use PdfGenerator\Backend\Catalog\Font;
use PdfGenerator\Backend\Catalog\Image;
use PdfGenerator\Backend\Structure\Document\Base\BaseDocumentStructure;
use PdfGenerator\Backend\Structure\DocumentVisitor;

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
     * @var DocumentVisitor
     */
    private $documentContentVisitor;

    /**
     * DocumentResources constructor.
     *
     * @param DocumentVisitor $documentContentVisitor
     */
    public function __construct(DocumentVisitor $documentContentVisitor)
    {
        $this->documentContentVisitor = $documentContentVisitor;
    }

    /**
     * @param \PdfGenerator\IR\Structure\Font $structure
     *
     * @return Font
     */
    public function getFont(\PdfGenerator\Backend\Structure\Font $structure)
    {
        return $this->getOrCreate($structure, $this->fontCache);
    }

    /**
     * @param \PdfGenerator\IR\Structure\Image $structure
     *
     * @return Image
     */
    public function getImage(\PdfGenerator\Backend\Structure\Document\Image $structure)
    {
        return $this->getOrCreate($structure, $this->imageCache);
    }

    /**
     * @param BaseDocumentStructure $structure
     * @param BaseDocumentStructure[] $cache
     *
     * @return BaseDocumentStructure|mixed
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
