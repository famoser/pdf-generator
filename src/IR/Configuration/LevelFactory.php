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

use PdfGenerator\IR\Configuration\Level\PageLevelRepository;
use PdfGenerator\IR\Configuration\Level\TextLevelRepository;

class LevelFactory
{
    /**
     * @var PageLevelRepository
     */
    private $pageLevelRepository;

    /**
     * @var TextLevelRepository
     */
    private $textLevelRepository;

    /**
     * TextBuilder constructor.
     *
     * @param StateFactory $stateFactory
     */
    public function __construct(StateFactory $stateFactory)
    {
        $this->pageLevelRepository = new PageLevelRepository($stateFactory->getGeneralGraphicStateRepository(), $stateFactory->getColorStateRepository());
        $this->textLevelRepository = new TextLevelRepository($stateFactory->getGeneralGraphicStateRepository(), $stateFactory->getColorStateRepository(), $stateFactory->getTextStateRepository());
    }

    /**
     * @return PageLevelRepository
     */
    public function getPageLevelRepository(): PageLevelRepository
    {
        return $this->pageLevelRepository;
    }

    /**
     * @return TextLevelRepository
     */
    public function getTextLevelRepository(): TextLevelRepository
    {
        return $this->textLevelRepository;
    }
}
