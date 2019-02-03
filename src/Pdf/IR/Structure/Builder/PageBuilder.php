<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdf\IR\Structure\Builder;

use Pdf\IR\Structure\Builder\Base\BaseBuilder;
use Pdf\IR\Structure\Page;
use Pdf\IR\Structure\Pages;

class PageBuilder extends BaseBuilder
{
    /**
     * @var Pages
     */
    private $pages;

    /**
     * @var int[]|float[]
     */
    private $mediaBox;

    /**
     * @var ResourcesBuilder
     */
    private $resourcesBuilder;

    /**
     * @var ContentsBuilder
     */
    private $contentsBuilder;

    /**
     * PageBuilder constructor.
     *
     * @param Pages $parent
     * @param ResourcesBuilder $resourcesBuilder
     * @param ContentsBuilder $contentsBuilder
     */
    public function __construct(Pages $parent, ResourcesBuilder $resourcesBuilder, ContentsBuilder $contentsBuilder)
    {
        $this->pages = $parent;
        $this->resourcesBuilder = $resourcesBuilder;
        $this->contentsBuilder = $contentsBuilder;
    }

    /**
     * @param float|int $width
     * @param float|int $height
     */
    public function setMediaBox($width, $height)
    {
        $this->mediaBox = [0, 0, $width, $height];
    }

    /**
     * @return ContentsBuilder
     */
    public function getContentsBuilder(): ContentsBuilder
    {
        return $this->contentsBuilder;
    }

    /**
     * @throws \Exception
     *
     * @return Page
     */
    public function build()
    {
        return parent::build();
    }

    /**
     * @throws \Exception
     *
     * @return mixed
     */
    protected function construct()
    {
        return new Page($this->pages, $this->mediaBox, $this->resourcesBuilder->build(), $this->contentsBuilder->build());
    }
}
