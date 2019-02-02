<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdf\IR\Structure;

class Page
{
    /**
     * @var Pages
     */
    private $parent;

    /**
     * @var int[]
     */
    private $mediaBox;

    /**
     * @var Contents
     */
    private $contents;

    /**
     * @var Resources
     */
    private $resources;

    /**
     * Page constructor.
     *
     * @param Pages $parent
     * @param array $mediaBox
     * @param Resources $resources
     * @param Contents $contents
     */
    public function __construct(Pages $parent, array $mediaBox, Resources $resources, Contents $contents)
    {
        $this->parent = $parent;
        $this->mediaBox = $mediaBox;
        $this->contents = $contents;
        $this->resources = $resources;
    }
}
