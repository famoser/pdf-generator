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

class ResourcesBuilder extends BaseBuilder
{
    /**
     * @var Resources
     */
    private $resources;

    /**
     * ResourcesBuilder constructor.
     */
    public function __construct()
    {
        $this->resources = new Resources();
    }

    /**
     * @return Resources
     */
    public function getResources(): Resources
    {
        return $this->resources;
    }

    /**
     * @throws \Exception
     *
     * @return Resources
     */
    protected function construct(): Resources
    {
        return $this->resources;
    }
}
