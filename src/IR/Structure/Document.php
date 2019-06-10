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

use PdfGenerator\IR\DocumentVisitor;
use PdfGenerator\IR\Structure\Font\DefaultFont;
use PdfGenerator\IR\Structure\Font\EmbeddedFont;

class Document
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
     * @return \PdfGenerator\Backend\Structure\Document
     */
    public function render()
    {
        $document = new \PdfGenerator\Backend\Structure\Document();

        $documentVisitor = new DocumentVisitor();
        foreach ($this->pages as $page) {
            $page = $page->accept($documentVisitor);
            $document->addPage($page);
        }

        return $document;
    }

    /**
     * @return string
     */
    public function save()
    {
        return $this->render()->save();
    }
}
