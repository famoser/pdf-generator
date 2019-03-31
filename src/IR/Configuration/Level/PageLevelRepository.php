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

use PdfGenerator\Backend\Content\Operators\Level\PageLevel;
use PdfGenerator\IR\Configuration\State\ColorStateRepository;
use PdfGenerator\IR\Configuration\State\GeneralGraphicStateRepository;

class PageLevelRepository
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
     * PageLevelRepository constructor.
     *
     * @param ColorStateRepository $colorStateRepository
     * @param GeneralGraphicStateRepository $generalGraphicStateRepository
     */
    public function __construct(GeneralGraphicStateRepository $generalGraphicStateRepository, ColorStateRepository $colorStateRepository)
    {
        $this->generalGraphicStateRepository = $generalGraphicStateRepository;
        $this->colorStateRepository = $colorStateRepository;
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
}
