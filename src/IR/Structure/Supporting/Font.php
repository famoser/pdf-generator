<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Supporting;

class Font
{
    const SUBTYPE_TYPE1 = 'Type1';
    const BASE_FONT_HELVETICA = 'Helvetica';

    /**
     * @var string
     */
    private $subtype;

    /**
     * @var string
     */
    private $baseFont;

    /**
     * @var string
     */
    private $identifier;

    /**
     * Font constructor.
     *
     * @param string $subtype
     * @param string $baseFont
     * @param string $identifier
     */
    public function __construct(string $subtype, string $baseFont, string $identifier)
    {
        $this->subtype = $subtype;
        $this->baseFont = $baseFont;
        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}
