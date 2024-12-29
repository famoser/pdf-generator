<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Backend\Structure\Document\Xmp;

readonly class Pdf
{
    public function __construct(private ?string $keywords)
    {
    }

    public static function createEmpty(): self
    {
        return new self(null);
    }

    public function getKeywords(): string
    {
        return $this->keywords;
    }
}
