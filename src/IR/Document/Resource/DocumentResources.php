<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\IR\Document\Resource;

use Famoser\PdfGenerator\Backend\Structure\Document\Font as BackendFont;
use Famoser\PdfGenerator\Backend\Structure\Document\Image as BackendImage;
use Famoser\PdfGenerator\IR\Document\Base\BaseDocumentResource;
use Famoser\PdfGenerator\IR\DocumentVisitor;

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
     * @param array<string, mixed> $cache
     */
    private function getOrCreate(BaseDocumentResource $structure, array &$cache): mixed
    {
        $identifier = $structure->getIdentifier();

        if (!\array_key_exists($identifier, $cache)) {
            $font = $structure->accept($this->documentContentVisitor);

            $cache[$identifier] = $font;
        }

        return $cache[$identifier];
    }
}
