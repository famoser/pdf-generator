<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdf\IR\Structure;

use Pdf\Backend\Object\Base\BaseObject;
use Pdf\Backend\Structure\File;
use Pdf\IR\Structure\Base\BaseStructure;
use Pdf\IR\Structure\Supporting\Font;
use Pdf\IR\StructureVisitor;

class Resources extends BaseStructure
{
    /**
     * @var int
     */
    private $resourceCounter;

    /**
     * @var Font[]
     */
    private $fonts = [];

    /**
     * @param string $subtype
     * @param string $baseFont
     */
    public function addFont(string $subtype, string $baseFont)
    {
        $identifier = $this->generateIdentifier('F');
        $this->fonts[$identifier] = new Font($subtype, $baseFont, $identifier);
    }

    /**
     * @param string $prefix
     *
     * @return string
     */
    private function generateIdentifier(string $prefix)
    {
        return $prefix . $this->resourceCounter++;
    }

    /**
     * @param StructureVisitor $visitor
     * @param File $file
     *
     * @return BaseObject
     */
    public function accept(StructureVisitor $visitor, File $file): BaseObject
    {
        return $visitor->visitResources($this, $file);
    }

    /**
     * @return Font[]
     */
    public function getFonts(): array
    {
        return $this->fonts;
    }
}
