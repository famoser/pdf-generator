<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Builder;

use PdfGenerator\Backend\Structure\Builder\Base\BaseBuilder;
use PdfGenerator\Backend\Structure\Resources;
use PdfGenerator\Backend\Structure\Supporting\FontCollection;

class ResourcesBuilder extends BaseBuilder
{
    /**
     * @var Resources
     */
    private $resources;

    /**
     * @var FontCollection
     */
    private $fontCollection;

    /**
     * ResourcesBuilder constructor.
     */
    public function __construct()
    {
        $this->resources = new Resources();
        $this->fontCollection = new FontCollection($this->resources);
    }

    /**
     * @return FontCollection
     */
    public function getFontCollection(): FontCollection
    {
        return $this->fontCollection;
    }

    /**
     * @throws \Exception
     *
     * @return mixed
     */
    protected function construct()
    {
        return $this->resources;
    }

    /**
     * @throws \Exception
     *
     * @return Resources
     */
    public function build()
    {
        return parent::build();
    }
}
