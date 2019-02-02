<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdf\IR;

use Pdf\Backend\File;
use Pdf\Backend\Object\Base\BaseObject;
use Pdf\IR\Structure\Catalog;

class Document
{
    /**
     * @var Catalog
     */
    private $catalog;

    /**
     * Document constructor.
     */
    public function __construct()
    {
        $this->catalog = new Catalog();
    }

    /**
     * @return BaseObject
     */
    public function render(): string
    {
        $structureVisitor = new StructureVisitor();
        $file = new File();

        $catalog = $structureVisitor->visitCatalog($this->catalog, $file);

        return $file->render($catalog);
    }

    /**
     * @return Catalog
     */
    public function getCatalog(): Catalog
    {
        return $this->catalog;
    }
}
