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

use PdfGenerator\Backend\Catalog\Font;
use PdfGenerator\Backend\Catalog\Image;
use PdfGenerator\Backend\Structure\Operators\Level\DrawingState;
use PdfGenerator\Backend\Structure\Operators\Level\WritingState;
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
     * @param \PdfGenerator\IR\Structure\Font $structure
     *
     * @return Font
     */
    public function getFont(\PdfGenerator\IR\Structure\Font $structure)
    {
        return $this->documentResources->getFont($structure);
    }

    /**
     * @param \PdfGenerator\IR\Structure\Image $structure
     *
     * @return Image
     */
    public function getImage(\PdfGenerator\IR\Structure\Image $structure)
    {
        return $this->documentResources->getImage($structure);
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
}
