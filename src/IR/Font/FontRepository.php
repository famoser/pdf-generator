<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Font;

use PdfGenerator\Backend\Document;

class FontRepository
{
    /**
     * @var Document
     */
    private $document;

    /**
     * FontRepository constructor.
     *
     * @param Document $document
     */
    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    /**
     * @param string $familyName
     *
     * @return \PdfGenerator\Backend\Structure\Font
     */
    public function get(string $familyName)
    {
        \assert($familyName === 'Helvetica');

        return $this->document->getResourcesBuilder()->getFontCollection()->getHelvetica();
    }
}
