<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure2;

use PdfGenerator\IR\Structure2\Base\BaseStructure2;
use PdfGenerator\IR\Structure2\Content\Base\BaseContent;

class Page extends BaseStructure2
{
    /**
     * @var int
     */
    private $pageNumber;

    /**
     * @var BaseContent[]
     */
    private $content;

    /**
     * Page constructor.
     *
     * @param int $pageNumber
     */
    public function __construct(int $pageNumber)
    {
        $this->pageNumber = $pageNumber;
    }

    /**
     * @param BaseContent $baseContent
     */
    public function addContent(BaseContent $baseContent)
    {
        $this->content[] = $baseContent;
    }

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->pageNumber;
    }

    /**
     * @return BaseContent[]
     */
    public function getContent(): array
    {
        return $this->content;
    }
}
