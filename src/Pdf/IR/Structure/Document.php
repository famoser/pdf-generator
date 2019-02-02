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
use Pdf\IR\StructureVisitor;

class Document extends BaseStructure
{
    /**
     * @var Catalog
     */
    private $catalog;

    /**
     * Document constructor.
     *
     * @param Catalog $catalog
     */
    public function __construct(Catalog $catalog)
    {
        $this->catalog = $catalog;
    }

    /**
     * @param StructureVisitor $visitor
     * @param File $file
     *
     * @return BaseObject
     */
    public function accept(StructureVisitor $visitor, File $file): BaseObject
    {
        return $visitor->visitDocument($this, $file);
    }

    /**
     * @return Catalog
     */
    public function getCatalog(): Catalog
    {
        return $this->catalog;
    }
}
