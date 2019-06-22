<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Document;

use PdfGenerator\Backend\Catalog\Font as CatalogFont;
use PdfGenerator\Backend\Catalog\Image as CatalogImage;
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
     * @param Font $structure
     *
     * @return CatalogFont
     */
    public function getFont(Font $structure)
    {
        return $this->getOrCreate($structure, $this->fontCache);
    }

    /**
     * @param Image $structure
     *
     * @return CatalogImage
     */
    public function getImage(Image $structure)
    {
        return $this->getOrCreate($structure, $this->imageCache);
    }

    /**
     * @param BaseDocumentStructure $structure
     * @param BaseDocumentStructure[] $cache
     *
     * @return BaseDocumentStructure|mixed
     */
    private function getOrCreate($structure, array &$cache)
    {
        $identifier = spl_object_id($structure);

        if (!\array_key_exists($identifier, $cache)) {
            $font = $structure->accept($this->documentContentVisitor);

            $cache[$identifier] = $font;
        }

        return $cache[$identifier];
    }
}
