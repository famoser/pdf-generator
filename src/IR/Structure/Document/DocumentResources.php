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
     * @var BackendFont[]
     */
    private array $fontCache = [];

    /**
     * @var BackendImage[]
     */
    private array $imageCache = [];

    /**
     * DocumentResources constructor.
     */
    public function __construct(private readonly DocumentVisitor $documentContentVisitor)
    {
    }

    public function getFont(Font $structure): BackendFont
    {
        return $this->getOrCreate($structure, $this->fontCache);
    }

    public function getImage(Image $structure): BackendImage
    {
        return $this->getOrCreate($structure, $this->imageCache);
    }

    /**
     * @param BackendFont[]|BackendImage[] $cache
     */
    private function getOrCreate(BaseDocumentStructure $structure, array &$cache): BackendImage|BackendFont
    {
        $identifier = $structure->getIdentifier();

        if (!\array_key_exists($identifier, $cache)) {
            $font = $structure->accept($this->documentContentVisitor);

            $cache[$identifier] = $font;
        }

        return $cache[$identifier];
    }
}
