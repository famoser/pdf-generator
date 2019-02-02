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

use Pdf\Backend\Object\Base\BaseObject;
use Pdf\IR\Structure\Page;

class StructureVisitor
{
    /**
     * @param Structure\Catalog $param
     * @param \Pdf\Backend\Structure\File $file
     *
     * @return BaseObject
     */
    public function visitCatalog(Structure\Catalog $param, \Pdf\Backend\Structure\File $file): BaseObject
    {
        $dictionary = $file->addDictionaryObject();
        $dictionary->addTextEntry('Type', 'Catalog');

        $pagesElement = $param->getPages()->accept($this, $file);

        $dictionary->addReferenceEntry('Pages', $pagesElement);

        return $dictionary;
    }

    /**
     * @param Structure\Pages $param
     * @param \Pdf\Backend\Structure\File $file
     *
     * @return BaseObject
     */
    public function visitPages(Structure\Pages $param, \Pdf\Backend\Structure\File $file): BaseObject
    {
        $dictionary = $file->addDictionaryObject();
        $dictionary->addTextEntry('Type', 'Pages');

        /** @var Page[] $kids */
        $kids = [];
        foreach ($param->getKids() as $kid) {
            $kids[] = $kid->accept($this, $file);
        }

        $dictionary->addReferenceArrayEntry('Kids', $kids);
        $dictionary->addNumberEntry('Count', \count($kids));

        return $dictionary;
    }
}
