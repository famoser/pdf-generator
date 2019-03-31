<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Configuration;

use PdfGenerator\IR\Configuration\State\ColorStateRepository;
use PdfGenerator\IR\Configuration\State\GeneralGraphicStateRepository;
use PdfGenerator\IR\Configuration\State\TextStateRepository;
use PdfGenerator\IR\Structure\Content\FontRepository;

class StateFactory
{
    /**
     * @var FontRepository
     */
    private $fontRepository;

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
     * TextBuilder constructor.
     *
     * @param FontRepository $fontRepository
     */
    public function __construct(FontRepository $fontRepository)
    {
        $this->fontRepository = $fontRepository;

        $this->generalGraphicStateRepository = new GeneralGraphicStateRepository();
        $this->colorStateRepository = new ColorStateRepository();
        $this->textStateRepository = new TextStateRepository($this->fontRepository);
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
}
