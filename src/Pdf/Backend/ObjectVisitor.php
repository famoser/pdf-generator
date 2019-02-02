<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdf\Backend;

use Pdf\Backend\Object\Base\BaseObject;
use Pdf\Backend\Object\DictionaryObject;
use Pdf\Backend\Object\StreamObject;

class ObjectVisitor
{
    /**
     * @var TokenVisitor
     */
    private $tokenVisitor;

    /**
     * ObjectVisitor constructor.
     */
    public function __construct()
    {
        $this->tokenVisitor = new TokenVisitor();
    }

    /**
     * @param DictionaryObject $dictionary
     *
     * @return string
     */
    public function visitDictionary(DictionaryObject $dictionary): string
    {
        return $this->visitObject($dictionary, $dictionary->getDictionaryToken()->accept($this->tokenVisitor));
    }

    /**
     * @param StreamObject $param
     *
     * @return string
     */
    public function visitStream(StreamObject $param): string
    {
        $lines = [];
        $lines[] = $param->getMetaData()->accept($this->tokenVisitor);
        $lines[] = 'stream';
        $lines[] = $param->getContent();
        $lines[] = 'endstream';

        return $this->visitObject($param, "\n" . implode('', $lines));
    }

    /**
     * @param BaseObject $object
     *
     * @return string
     */
    private function visitObject(BaseObject $object, string $content)
    {
        return $object->getNumber() . ' 0 obj' . $content . "\nendobj";
    }
}
