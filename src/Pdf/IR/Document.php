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

class Document
{
    /**
     * @var Catalog
     */
    private $catalog;

    /**
     * Document constructor.
     *
     * @param Catalog $catalog
     */
    public function __construct(Catalog $catalog)
    {
        $this->catalog = $catalog;
    }
}
