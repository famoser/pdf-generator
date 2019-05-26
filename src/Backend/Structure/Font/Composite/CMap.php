<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Font\Composite;

class CMap
{
    /**
     * the name of that CMap
     * must equal to the name specified in the stream from useCMan.
     *
     * @var string
     */
    private $cMapName;

    /**
     * the system info which must match with the one specified on the CIDFont.
     *
     * @var CIDSystemInfo
     */
    private $cIDSystemInfo;

    /**
     * the stream containing the actual cmap information.
     *
     * @var string
     */
    private $useCMap;
}
