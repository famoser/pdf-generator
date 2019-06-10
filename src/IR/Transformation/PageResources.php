<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Transformation;

use PdfGenerator\Backend\Content\Operators\Level\PageLevel;
use PdfGenerator\Backend\Content\Operators\Level\TextLevel;
use PdfGenerator\Backend\Structure\Font;
use PdfGenerator\Backend\Structure\Image;
use PdfGenerator\IR\Configuration\State\ColorStateRepository;
use PdfGenerator\IR\Configuration\State\GeneralGraphicStateRepository;
use PdfGenerator\IR\Configuration\State\TextStateRepository;

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
     * @var Font[]
     */
    private $fonts;

    /**
     * @var Image[]
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
     * @param \PdfGenerator\IR\Structure2\Font $structure
     *
     * @return Font
     */
    public function getFont(\PdfGenerator\IR\Structure2\Font $structure)
    {
        $font = $this->documentResources->getFont($structure);
        $this->fonts[$font->getIdentifier()] = $font;

        return $font;
    }

    /**
     * @param \PdfGenerator\IR\Structure2\Image $structure
     *
     * @return Image
     */
    public function getImage(\PdfGenerator\IR\Structure2\Image $structure)
    {
        $image = $this->documentResources->getImage($structure);
        $this->images[$image->getIdentifier()] = $image;

        return $image;
    }

    /**
     * @return PageLevel
     */
    public function getPageLevel()
    {
        $generalGraphicState = $this->generalGraphicStateRepository->getGeneralGraphicState();
        $colorState = $this->colorStateRepository->getColorState();

        return new PageLevel($generalGraphicState, $colorState);
    }

    /**
     * @return TextLevel
     */
    public function getTextLevel()
    {
        $generalGraphicState = $this->generalGraphicStateRepository->getGeneralGraphicState();
        $colorState = $this->colorStateRepository->getColorState();
        $textState = $this->textStateRepository->getTextState();

        return new TextLevel($generalGraphicState, $colorState, $textState);
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
