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
     * @var CatalogFont[]
     */
    private array $fontCache = [];

    /**
     * @var CatalogImage[]
     */
    private array $imageCache = [];

    public function __construct(private readonly DocumentVisitor $documentContentVisitor)
    {
    }

    public function getFont(Font $structure): CatalogFont
    {
        return $this->getOrCreate($structure, $this->fontCache);
    }

    public function getImage(Image $structure): CatalogImage
    {
        return $this->getOrCreate($structure, $this->imageCache);
    }

    /**
     * @template T
     *
     * @param array<string, T> $cache
     *
     * @return T
     */
    private function getOrCreate(BaseDocumentStructure $structure, array &$cache): mixed
    {
        $identifier = spl_object_id($structure);

        if (!\array_key_exists($identifier, $cache)) {
            $entry = $structure->accept($this->documentContentVisitor);

            $cache[$identifier] = $entry;
        }

        return $cache[$identifier];
    }
}
