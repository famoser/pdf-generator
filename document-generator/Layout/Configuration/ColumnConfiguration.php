<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DocumentGenerator\Layout\Configuration;

class ColumnConfiguration
{
    public const SIZING_BY_TEXT = 'sizing_by_text';
    public const SIZING_EXPAND = 'sizing_expand';

    public function __construct(private string $sizing = self::SIZING_EXPAND, private ?string $text = null)
    {
    }

    public function getSizing(): string
    {
        return $this->sizing;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
