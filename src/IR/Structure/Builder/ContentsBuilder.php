<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Builder;

use PdfGenerator\IR\Content\Base\BaseContent;
use PdfGenerator\IR\Structure\Builder\Base\BaseBuilder;
use PdfGenerator\IR\Structure\Contents;

class ContentsBuilder extends BaseBuilder
{
    /**
     * @var BaseContent
     */
    private $content;

    /**
     * @param BaseContent $content
     */
    public function setContent(BaseContent $content)
    {
        $this->content = $content;
    }

    /**
     * @throws \Exception
     *
     * @return mixed
     */
    protected function construct()
    {
        if ($this->content === null) {
            $this->throwForMissingField('content');
        }

        return new Contents($this->content);
    }

    /**
     * @throws \Exception
     *
     * @return Contents
     */
    public function build()
    {
        return parent::build();
    }
}
