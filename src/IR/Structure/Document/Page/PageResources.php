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
    /**
     * @var GeneralGraphicStateRepository
     */
    private $generalGraphicStateRepository;

    /**
     * @var ColorStateRepository
     */
    private $colorStateRepository;

    /**
     * @var TextStateRepository
     */
    private $textStateRepository;

    /**
     * @var DocumentResources
     */
    private $documentResources;

    /**
     * @var BackendFont[]
     */
    private $fonts;

    /**
     * @var BackendImage[]
     */
    private $images;

    /**
     * PageResources constructor.
     *
     * @param DocumentResources $documentResources
     */
    public function __construct(DocumentResources $documentResources)
    {
        $this->documentResources = $documentResources;

        $this->generalGraphicStateRepository = new GeneralGraphicStateRepository();
        $this->colorStateRepository = new ColorStateRepository();
        $this->textStateRepository = new TextStateRepository();
    }

    /**
     * @return GeneralGraphicStateRepository
     */
    public function getGeneralGraphicStateRepository(): GeneralGraphicStateRepository
    {
        return $this->generalGraphicStateRepository;
    }

    /**
     * @return ColorStateRepository
     */
    public function getColorStateRepository(): ColorStateRepository
    {
        return $this->colorStateRepository;
    }

    /**
     * @return TextStateRepository
     */
    public function getTextStateRepository(): TextStateRepository
    {
        return $this->textStateRepository;
    }

    /**
     * @param Font $structure
     *
     * @return BackendFont
     */
    public function getFont(Font $structure)
    {
        $font = $this->documentResources->getFont($structure);
        $this->fonts[$structure->getIdentifier()] = $font;

        return $font;
    }

    /**
     * @param Image $structure
     *
     * @return BackendImage
     */
    public function getImage(Image $structure)
    {
        $image = $this->documentResources->getImage($structure);
        $this->images[$structure->getIdentifier()] = $image;

        return $image;
    }

    /**
     * @return DrawingState
     */
    public function getDrawingState()
    {
        $generalGraphicState = $this->generalGraphicStateRepository->getGeneralGraphicState();
        $colorState = $this->colorStateRepository->getColorState();

        return new DrawingState($generalGraphicState, $colorState);
    }

    /**
     * @return WritingState
     */
    public function getWritingState()
    {
        $generalGraphicState = $this->generalGraphicStateRepository->getGeneralGraphicState();
        $colorState = $this->colorStateRepository->getColorState();
        $textState = $this->textStateRepository->getTextState();

        return new WritingState($generalGraphicState, $colorState, $textState);
    }

    /**
     * @return Font[]
     */
    public function getFonts(): array
    {
        return $this->fonts;
    }

    /**
     * @return Image[]
     */
    public function getImages(): array
    {
        return $this->images;
    }
}
