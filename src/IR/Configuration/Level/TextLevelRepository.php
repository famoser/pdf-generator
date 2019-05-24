<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Configuration\Level;

use PdfGenerator\Backend\Content\Operators\Level\TextLevel;
use PdfGenerator\IR\Configuration\State\ColorStateRepository;
use PdfGenerator\IR\Configuration\State\GeneralGraphicStateRepository;
use PdfGenerator\IR\Configuration\State\TextStateRepository;

class TextLevelRepository
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
    private $texStateRepository;

    /**
     * PageLevelRepository constructor.
     *
     * @param GeneralGraphicStateRepository $generalGraphicStateRepository
     * @param ColorStateRepository $colorStateRepository
     * @param TextStateRepository $textStateRepository
     */
    public function __construct(GeneralGraphicStateRepository $generalGraphicStateRepository, ColorStateRepository $colorStateRepository, TextStateRepository $textStateRepository)
    {
        $this->generalGraphicStateRepository = $generalGraphicStateRepository;
        $this->colorStateRepository = $colorStateRepository;
        $this->texStateRepository = $textStateRepository;
    }

    /**
     * @throws \Exception
     *
     * @return TextLevel
     */
    public function getTextLevel()
    {
        $generalGraphicState = $this->generalGraphicStateRepository->getGeneralGraphicState();
        $colorState = $this->colorStateRepository->getColorState();
        $textState = $this->texStateRepository->getTextState();

        return new TextLevel($generalGraphicState, $colorState, $textState);
    }
}
