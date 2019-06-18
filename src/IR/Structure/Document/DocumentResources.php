<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Document;

use PdfGenerator\Backend\Structure\Document\Font as BackendFont;
use PdfGenerator\Backend\Structure\Document\Image as BackendImage;
use PdfGenerator\IR\Structure\Document\Base\BaseDocumentStructure;
use PdfGenerator\IR\Structure\DocumentVisitor;

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
     * @return BackendFont
     */
    public function getFont(Font $structure)
    {
        return $this->getOrCreate($structure, $this->fontCache);
    }

    /**
     * @param Image $structure
     *
     * @return BackendImage
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
