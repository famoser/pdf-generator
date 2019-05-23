<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\IR\Utils\Post;

class GlyphInfo
{
    /**
     * @var int|null
     */
    private $macintoshIndex;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @return int|null
     */
    public function getMacintoshIndex(): ?int
    {
        return $this->macintoshIndex;
    }

    /**
     * @param int|null $macintoshIndex
     */
    public function setMacintoshIndex(?int $macintoshIndex): void
    {
        $this->macintoshIndex = $macintoshIndex;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }
}
