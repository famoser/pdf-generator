<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Document\Page;

use PdfGenerator\Backend\Structure\Document\Font as BackendFont;
use PdfGenerator\Backend\Structure\Document\Image as BackendImage;
use PdfGenerator\Backend\Structure\Document\Page\StateCollections\DrawingState;
use PdfGenerator\Backend\Structure\Document\Page\StateCollections\WritingState;
use PdfGenerator\IR\Structure\Document\DocumentResources;
use PdfGenerator\IR\Structure\Document\Font;
use PdfGenerator\IR\Structure\Document\Image;
use PdfGenerator\IR\Structure\Document\Page\State\ColorStateRepository;
use PdfGenerator\IR\Structure\Document\Page\State\GeneralGraphicStateRepository;
use PdfGenerator\IR\Structure\Document\Page\State\TextStateRepository;

class PageResources
{
    private GeneralGraphicStateRepository $generalGraphicStateRepository;

    private ColorStateRepository $colorStateRepository;

    private TextStateRepository $textStateRepository;

    private DocumentResources $documentResources;

    /**
     * @var BackendFont[]
     */
    private array $fonts = [];

    /**
     * @var BackendImage[]
     */
    private array $images = [];

    /**
     * PageResources constructor.
     */
    public function __construct(DocumentResources $documentResources)
    {
        $this->documentResources = $documentResources;

        $this->generalGraphicStateRepository = new GeneralGraphicStateRepository();
        $this->colorStateRepository = new ColorStateRepository();
        $this->textStateRepository = new TextStateRepository();
    }

    public function getGeneralGraphicStateRepository(): GeneralGraphicStateRepository
    {
        return $this->generalGraphicStateRepository;
    }

    public function getColorStateRepository(): ColorStateRepository
    {
        return $this->colorStateRepository;
    }

    public function getTextStateRepository(): TextStateRepository
    {
        return $this->textStateRepository;
    }

    public function getFont(Font $structure): BackendFont
    {
        $font = $this->documentResources->getFont($structure);
        $this->fonts[$structure->getIdentifier()] = $font;

        return $font;
    }

    public function getImage(Image $structure): BackendImage
    {
        $image = $this->documentResources->getImage($structure);
        $this->images[$structure->getIdentifier()] = $image;

        return $image;
    }

    public function getDrawingState(): DrawingState
    {
        $generalGraphicState = $this->generalGraphicStateRepository->getGeneralGraphicState();
        $colorState = $this->colorStateRepository->getColorState();

        return new DrawingState($generalGraphicState, $colorState);
    }

    public function getWritingState(): WritingState
    {
        $generalGraphicState = $this->generalGraphicStateRepository->getGeneralGraphicState();
        $colorState = $this->colorStateRepository->getColorState();
        $textState = $this->textStateRepository->getTextState();

        return new WritingState($generalGraphicState, $colorState, $textState);
    }

    /**
     * @return BackendFont[]
     */
    public function getFonts(): array
    {
        return $this->fonts;
    }

    /**
     * @return BackendImage[]
     */
    public function getImages(): array
    {
        return $this->images;
    }
}
