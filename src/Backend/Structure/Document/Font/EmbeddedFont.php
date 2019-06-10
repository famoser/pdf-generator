<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Font;

use PdfGenerator\Backend\Structure\Document\Font\CharacterMapping;
use PdfGenerator\Backend\Structure\Font;

class EmbeddedFont extends Font
{
    /**
     * @var string
     */
    private $fontContent;

    /**
     * @var CharacterMapping[]
     */
    private $characterMappings;

    /**
     * EmbeddedFont constructor.
     *
     * @param string $fontContent
     * @param CharacterMapping[] $characterMappings
     */
    public function __construct(string $fontContent, array $characterMappings)
    {
        $this->fontContent = $fontContent;
        $this->characterMappings = $characterMappings;
    }
}
