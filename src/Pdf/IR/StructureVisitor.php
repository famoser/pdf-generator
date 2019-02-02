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
use Pdf\Backend\Token\DictionaryToken;
use Pdf\Backend\Token\ReferenceToken;
use Pdf\IR\Structure\Page;

class StructureVisitor
{
    /**
     * @var ContentVisitor
     */
    private $contentVisitor;

    /**
     * @var BaseObject[]
     */
    private $objectNodeLookup;

    /**
     * StructureVisitor constructor.
     *
     * @param ContentVisitor $contentVisitor
     */
    public function __construct(ContentVisitor $contentVisitor)
    {
        $this->contentVisitor = $contentVisitor;
    }

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
        $this->objectNodeLookup[spl_object_id($param)] = $dictionary;

        /** @var Page[] $kids */
        $kids = [];
        foreach ($param->getKids() as $kid) {
            $kids[] = $kid->accept($this, $file);
        }

        $dictionary->addReferenceArrayEntry('Kids', $kids);
        $dictionary->addNumberEntry('Count', \count($kids));

        return $dictionary;
    }

    /**
     * @param Page $param
     * @param \Pdf\Backend\Structure\File $file
     *
     * @return BaseObject
     */
    public function visitPage(Page $param, \Pdf\Backend\Structure\File $file): BaseObject
    {
        $dictionary = $file->addDictionaryObject();
        $dictionary->addTextEntry('Type', 'Page');

        $parentReference = $this->objectNodeLookup[spl_object_id($param->getParent())];
        $dictionary->addReferenceEntry('Parent', $parentReference);

        $resources = $param->getResources()->accept($this, $file);
        $dictionary->addReferenceEntry('Resources', $resources);

        $dictionary->addNumberArrayEntry('MediaBox', $param->getMediaBox());

        $contents = $param->getContents()->accept($this, $file);
        $dictionary->addReferenceEntry('Contents', $contents);

        return $dictionary;
    }

    /**
     * @param Structure\Resources $param
     * @param \Pdf\Backend\Structure\File $file
     *
     * @return BaseObject
     */
    public function visitResources(Structure\Resources $param, \Pdf\Backend\Structure\File $file): BaseObject
    {
        $dictionary = $file->addDictionaryObject();

        $fontDictionary = new DictionaryToken();
        foreach ($param->getFonts() as $font) {
            $fontReference = $this->objectNodeLookup[spl_object_id($font)];
            $fontDictionary->setEntry($font->getIdentifier(), new ReferenceToken($fontReference));
        }

        $dictionary->addDictionaryEntry('Font', $fontDictionary);

        return $dictionary;
    }

    /**
     * @param Structure\Contents $param
     * @param \Pdf\Backend\Structure\File $file
     *
     * @return BaseObject
     */
    public function visitContents(Structure\Contents $param, \Pdf\Backend\Structure\File $file): BaseObject
    {
        return $param->getContent()->accept($this->contentVisitor, $file);
    }

    /**
     * @param Structure\Document $param
     * @param \Pdf\Backend\Structure\File $file
     *
     * @return BaseObject
     */
    public function visitDocument(Structure\Document $param, \Pdf\Backend\Structure\File $file)
    {
        return $param->getCatalog()->accept($this, $file);
    }
}
