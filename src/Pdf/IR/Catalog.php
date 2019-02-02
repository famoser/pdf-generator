<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdf\IR;

class Catalog
{
    /**
     * @var Pages
     */
    private $pages;

    /**
     * Catalog constructor.
     */
    public function __construct()
    {
        $this->pages = new Pages();
    }

    /**
     * @return Pages
     */
    public function getPages(): Pages
    {
        return $this->pages;
    }
}
