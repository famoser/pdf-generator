<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Document\Layout\Configuration;

class ColumnConfiguration
{
    const SIZING_BY_TEXT = 'sizing_by_text';
    const SIZING_EXPAND = 'sizing_expand';

    /**
     * @var string
     */
    private $sizing;

    /**
     * @var string
     */
    private $text;

    /**
     * @param string $sizing
     * @param string|null $text
     */
    public function __construct(string $sizing = self::SIZING_EXPAND, string $text = null)
    {
        $this->sizing = $sizing;
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getSizing()
    {
        return $this->sizing;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
}
