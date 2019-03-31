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

use PdfGenerator\Backend\Document;
use PdfGenerator\IR\Structure\Content\FontRepository;
use PdfGenerator\IR\Structure\Content\ImageRepository;
use PdfGenerator\IR\Structure\Content\PageRepository;

class ContentFactory
{
    /**
     * @var PageRepository
     */
    private $pageRepository;

    /**
     * @var FontRepository
     */
    private $fontRepository;

    /**
     * @var ImageRepository
     */
    private $imageRepository;

    /**
     * @param Document $document
     */
    public function __construct(Document $document)
    {
        $this->pageRepository = new PageRepository($document);
        $this->fontRepository = new FontRepository($document);
        $this->imageRepository = new ImageRepository($document);
    }

    /**
     * @return PageRepository
     */
    public function getPageRepository(): PageRepository
    {
        return $this->pageRepository;
    }

    /**
     * @return FontRepository
     */
    public function getFontRepository(): FontRepository
    {
        return $this->fontRepository;
    }

    /**
     * @return ImageRepository
     */
    public function getImageRepository(): ImageRepository
    {
        return $this->imageRepository;
    }
}
