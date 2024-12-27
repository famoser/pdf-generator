<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\DocumentGenerator\Layout\Configuration;

class ColumnConfiguration
{
    final public const SIZING_BY_TEXT = 'sizing_by_text';
    final public const SIZING_EXPAND = 'sizing_expand';

    public function __construct(private readonly string $sizing = self::SIZING_EXPAND, private readonly ?string $text = null)
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
