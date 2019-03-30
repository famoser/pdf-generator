<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Content;

use PdfGenerator\Backend\Content\Operators\Level\PageLevel;
use PdfGenerator\Backend\Content\Operators\Level\TextLevel;
use PdfGenerator\Backend\Content\Operators\LevelTransitionVisitor;
use PdfGenerator\Backend\Content\Operators\State\ColorState;
use PdfGenerator\Backend\Content\Operators\State\GeneralGraphicState;
use PdfGenerator\Backend\Content\Operators\State\TextState;
use PdfGenerator\Backend\Structure\Page;

class GraphicStateRepository
{
    /**
     * @var LevelTransitionVisitor
     */
    private $levelTransitionVisitor;

    /**
     * @var PageLevel[]
     */
    private $pageLevelByPage = [];

    /**
     * @var TextLevel[]
     */
    private $textLevelByPage = [];

    /**
     * GraphicStateRepository constructor.
     */
    public function __construct()
    {
        $this->levelTransitionVisitor = new LevelTransitionVisitor();
    }

    /**
     * @param Page $page
     * @param TextLevel $newTextLevel
     *
     * @return string[]
     */
    public function applyTextLevelState(Page $page, TextLevel $newTextLevel)
    {
        // generate operators to get to next state
        $existing = $this->getTextLevelState($page);
        $operators = $this->levelTransitionVisitor->visitText($newTextLevel, $existing);

        // persist new state
        $this->setTextLevelState($page, $newTextLevel);

        return $operators;
    }

    /**
     * @param Page $page
     * @param PageLevel $newPageLevel
     *
     * @return string[]
     */
    public function applyPageLevelState(Page $page, PageLevel $newPageLevel)
    {
        // generate operators to get to next state
        $existing = $this->getPageLevelState($page);
        $operators = $this->levelTransitionVisitor->visitPage($newPageLevel, $existing);

        // persist new state
        $this->setPageLevelState($page, $newPageLevel);

        return $operators;
    }

    /**
     * @param Page $page
     *
     * @return PageLevel
     */
    private function getPageLevelState(Page $page)
    {
        $identifier = $page->getIdentifier();

        if (\array_key_exists($identifier, $this->pageLevelByPage)) {
            return $this->pageLevelByPage[$identifier];
        }

        $newPageLevel = new PageLevel(new GeneralGraphicState(), new ColorState());
        $this->pageLevelByPage[$identifier] = $newPageLevel;

        return $newPageLevel;
    }

    /**
     * @param Page $page
     *
     * @return TextLevel
     */
    private function getTextLevelState(Page $page)
    {
        $identifier = $page->getIdentifier();

        if (\array_key_exists($identifier, $this->textLevelByPage)) {
            return $this->textLevelByPage[$identifier];
        }

        $newTextLevel = new TextLevel(new GeneralGraphicState(), new ColorState(), new TextState());
        $this->textLevelByPage[$identifier] = $newTextLevel;

        return $newTextLevel;
    }

    /**
     * @param Page $page
     * @param PageLevel $pageLevel
     */
    private function setPageLevelState(Page $page, PageLevel $pageLevel)
    {
        $identifier = $page->getIdentifier();

        $this->pageLevelByPage[$identifier] = $pageLevel;

        $textLevel = $this->getTextLevelState($page);
        $textLevel->applyStateFromPage($pageLevel);
    }

    /**
     * @param Page $page
     * @param TextLevel $textLevel
     */
    private function setTextLevelState(Page $page, TextLevel $textLevel)
    {
        $identifier = $page->getIdentifier();

        $this->textLevelByPage[$identifier] = $textLevel;

        $pageLevel = $this->getPageLevelState($page);
        $pageLevel->applyStateFromText($textLevel);
    }
}
