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
use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\GeneralGraphicState;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\StateCollections\DrawingState;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\StateCollections\WritingState;
use Famoser\PdfGenerator\IR\Document\Resource\State\ColorStateRepository;
use Famoser\PdfGenerator\IR\Document\Resource\State\GeneralGraphicStateRepository;
use Famoser\PdfGenerator\IR\Document\Resource\State\TextStateRepository;

class PageResources
{
    private readonly GeneralGraphicStateRepository $generalGraphicStateRepository;

    private readonly ColorStateRepository $colorStateRepository;

    private readonly TextStateRepository $textStateRepository;

    /**
     * @var BackendFont[]
     */
    private array $fonts = [];

    /**
     * @var BackendImage[]
     */
    private array $images = [];

    public function __construct(private readonly DocumentResources $documentResources)
    {
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

    public function getGeneralGraphicState(): GeneralGraphicState
    {
        return $this->generalGraphicStateRepository->getGeneralGraphicState();
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
