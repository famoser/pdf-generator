<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Content;

use PdfGenerator\Backend\Content\Base\BaseContent;
use PdfGenerator\Backend\Content\Operators\Level\TextLevel;
use PdfGenerator\Backend\ContentVisitor;
use PdfGenerator\Backend\File\File;
use PdfGenerator\Backend\File\Object\Base\BaseObject;
use PdfGenerator\Backend\Structure\Page;

class TextContent extends BaseContent
{
    /**
     * @var string
     */
    private $text;

    /**
     * @var TextLevel
     */
    private $textLevel;

    /**
     * TextSymbol constructor.
     *
     * @param string $content
     * @param TextLevel $textLevel
     */
    public function __construct(string $content, TextLevel $textLevel)
    {
        $this->text = $content;
        $this->textLevel = $textLevel;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return TextLevel
     */
    public function getTextLevel(): TextLevel
    {
        return $this->textLevel;
    }

    /**
     * @param ContentVisitor $visitor
     * @param File $file
     * @param Page $page
     *
     * @return BaseObject
     */
    public function accept(ContentVisitor $visitor, File $file, Page $page): BaseObject
    {
        return $visitor->visitTextContent($this, $file, $page);
    }
}
