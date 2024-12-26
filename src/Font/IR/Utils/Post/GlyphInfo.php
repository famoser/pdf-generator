<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Font\IR\Utils\Post;

class GlyphInfo
{
    private ?int $macintoshIndex = null;

    private ?string $name = null;

    public function getMacintoshIndex(): ?int
    {
        return $this->macintoshIndex;
    }

    public function setMacintoshIndex(?int $macintoshIndex): void
    {
        $this->macintoshIndex = $macintoshIndex;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }
}
