<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Backend\Structure\Document\Page\StateCollections\Base;

use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\Base\BaseState;

abstract readonly class BaseStateCollection
{
    /**
     * @return BaseState[]
     */
    abstract public function getState(): array;
}
