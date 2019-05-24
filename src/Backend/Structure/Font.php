<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure;

use PdfGenerator\Backend\File\File;
use PdfGenerator\Backend\Structure\Base\BaseStructure;
use PdfGenerator\Backend\Structure\Base\IdentifiableStructureTrait;

abstract class Font extends BaseStructure
{
    use IdentifiableStructureTrait;

    /**
     * @var string
     */
    private $subtype;

    /**
     * @var string
     */
    private $baseFont;

    /**
     * File constructor.
     *
     * @param string $identifier
     * @param string $subtype
     * @param string $baseFont
     */
    public function __construct(string $identifier, string $subtype, string $baseFont)
    {
        $this->setIdentifier($identifier);

        $this->subtype = $subtype;
        $this->baseFont = $baseFont;
    }

    /**
     * @return string
     */
    public function getSubtype(): string
    {
        return $this->subtype;
    }

    /**
     * @return string
     */
    public function getBaseFont(): string
    {
        return $this->baseFont;
    }
}
