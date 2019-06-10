<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure;

use PdfGenerator\IR\Structure\Base\BaseStructure2;
use PdfGenerator\IR\Structure\Font\DefaultFont;
use PdfGenerator\IR\Structure\Font\EmbeddedFont;
use PdfGenerator\IR\StructureVisitor;

class Document extends BaseStructure2
{
    /**
     * @var Page[]
     */
    private $pages = [];

    /**
     * @var Image[]
     */
    private $images = [];

    /**
     * @var DefaultFont[]
     */
    private $defaultFonts = [];

    /**
     * @var EmbeddedFont[]
     */
    private $embeddedFonts = [];

    /**
     * @return Page[]
     */
    public function getPages(): array
    {
        return $this->pages;
    }

    /**
     * @param int $pageNumber
     *
     * @return Page
     */
    public function getOrCreatePage(int $pageNumber): Page
    {
        $maxPageNumber = \count($this->pages);

        while ($pageNumber > $maxPageNumber) {
            $this->pages[] = new Page($maxPageNumber);
            ++$maxPageNumber;
        }

        return $this->pages[$pageNumber - 1];
    }

    /**
     * @return Image[]
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @param string $imagePath
     *
     * @return Image
     */
    public function getOrCreateImage(string $imagePath): Image
    {
        if (!\array_key_exists($imagePath, $this->images)) {
            $this->images[$imagePath] = new Image($imagePath);
        }

        return $this->images[$imagePath];
    }

    /**
     * @return DefaultFont[]
     */
    public function getDefaultFonts(): array
    {
        return $this->defaultFonts;
    }

    /**
     * @param string $font
     * @param string $style
     *
     * @return DefaultFont
     */
    public function getOrCreateDefaultFont(string $font, string $style): DefaultFont
    {
        $key = $font . '_' . $style;
        if (!\array_key_exists($key, $this->images)) {
            $this->defaultFonts[$key] = new DefaultFont($font, $style);
        }

        return $this->defaultFonts[$key];
    }

    /**
     * @return EmbeddedFont[]
     */
    public function getEmbeddedFonts(): array
    {
        return $this->embeddedFonts;
    }

    /**
     * @param string $fontPath
     *
     * @return EmbeddedFont
     */
    public function getOrCreateEmbeddedFont(string $fontPath): EmbeddedFont
    {
        if (!\array_key_exists($fontPath, $this->embeddedFonts)) {
            $this->embeddedFonts[$fontPath] = new EmbeddedFont($fontPath);
        }

        return $this->embeddedFonts[$fontPath];
    }

    /**
     * @param StructureVisitor $visitor
     *
     * @return mixed
     */
    public function accept(StructureVisitor $visitor)
    {
        return $visitor->visitDocument($this);
    }
}
