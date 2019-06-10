<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend;

use PdfGenerator\Backend\File\File;
use PdfGenerator\Backend\File\Object\Base\BaseObject;
use PdfGenerator\Backend\File\Object\DictionaryObject;
use PdfGenerator\Backend\File\Object\StreamObject;
use PdfGenerator\Backend\File\Token\DictionaryToken;
use PdfGenerator\Backend\File\Token\ReferenceToken;
use PdfGenerator\Backend\Structure\Base\IdentifiableStructureTrait;
use PdfGenerator\Backend\Structure\Catalog;
use PdfGenerator\Backend\Structure\ContentVisitor;
use PdfGenerator\Backend\Structure\Font\Structure\CIDSystemInfo;
use PdfGenerator\Backend\Structure\Font\Structure\CMap;
use PdfGenerator\Backend\Structure\Font\Structure\FontDescriptor;
use PdfGenerator\Backend\Structure\Page;

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
     */
    public function __construct()
    {
        $this->contentVisitor = new ContentVisitor();
    }

    /**
     * @param Catalog $catalog
     *
     * @return string
     */
    public static function renderCatalog(Catalog $catalog)
    {
        $structureVisitor = new self();
        $file = new File();

        $catalog = $structureVisitor->visitCatalog($catalog, $file);

        return $file->render($catalog);
    }

    /**
     * @param Structure\Catalog $structure
     * @param File $file
     *
     * @return BaseObject
     */
    public function visitCatalog(Structure\Catalog $structure, File $file): BaseObject
    {
        $dictionary = $file->addDictionaryObject();
        $dictionary->addTextEntry('Type', 'Catalog');

        $pagesElement = $structure->getPages()->accept($this, $file);

        $dictionary->addReferenceEntry('Pages', $pagesElement);

        return $dictionary;
    }

    /**
     * @param Structure\Pages $structure
     * @param File $file
     *
     * @return BaseObject
     */
    public function visitPages(Structure\Pages $structure, File $file): BaseObject
    {
        $dictionary = $file->addDictionaryObject();
        $dictionary->addTextEntry('Type', 'Pages');
        $this->objectNodeLookup[spl_object_id($structure)] = $dictionary;

        /** @var Page[] $kids */
        $kids = [];
        foreach ($structure->getKids() as $kid) {
            $kids[] = $kid->accept($this, $file);
        }

        $dictionary->addReferenceArrayEntry('Kids', $kids);
        $dictionary->addNumberEntry('Count', \count($kids));

        return $dictionary;
    }

    /**
     * @param Page $structure
     * @param File $file
     *
     * @return BaseObject
     */
    public function visitPage(Page $structure, File $file): BaseObject
    {
        $dictionary = $file->addDictionaryObject();
        $dictionary->addTextEntry('Type', 'Page');

        $parentReference = $this->objectNodeLookup[spl_object_id($structure->getParent())];
        $dictionary->addReferenceEntry('Parent', $parentReference);

        $resources = $structure->getResources()->accept($this, $file);
        $dictionary->addReferenceEntry('Resources', $resources);

        $dictionary->addNumberArrayEntry('MediaBox', $structure->getMediaBox());

        $contents = $structure->getContents()->accept($this, $file, $structure);
        $dictionary->addReferenceArrayEntry('Contents', $contents);

        return $dictionary;
    }

    /**
     * @param Structure\Resources $structure
     * @param File $file
     *
     * @return BaseObject
     */
    public function visitResources(Structure\Resources $structure, File $file): BaseObject
    {
        $dictionary = $file->addDictionaryObject();

        // see PDF320000_2008 14.2
        $procSet = ['PDF'];

        if (\count($structure->getType1Fonts()) > 0) {
            $fontDictionary = $this->createReferenceDictionary($structure->getType1Fonts(), $file);
            $dictionary->addDictionaryEntry('Font', $fontDictionary);
            $procSet[] = 'Text';
        }

        if (\count($structure->getImages()) > 0) {
            $fontDictionary = $this->createReferenceDictionary($structure->getImages(), $file);
            $dictionary->addDictionaryEntry('XObject', $fontDictionary);
            $procSet[] = 'ImageC';
        }

        $dictionary->addTextArrayEntry('ProcSet', $procSet, '/');

        return $dictionary;
    }

    /**
     * @var ReferenceToken[]
     */
    private $referenceLookup;

    /**
     * @param IdentifiableStructureTrait[] $structures
     * @param File $file
     *
     * @return DictionaryToken
     */
    private function createReferenceDictionary(array $structures, File $file)
    {
        $dictionary = new DictionaryToken();
        foreach ($structures as $structure) {
            $identifier = $structure->getIdentifier();

            if (!\array_key_exists($identifier, $this->referenceLookup)) {
                $reference = $structure->accept($this, $file);
                $this->referenceLookup[$identifier] = new ReferenceToken($reference);
            }

            $referenceToken = $this->referenceLookup[$identifier];
            $dictionary->setEntry($structure->getIdentifier(), $referenceToken);
        }

        return $dictionary;
    }

    /**
     * @param Structure\Contents $structure
     * @param File $file
     * @param Page $page
     *
     * @return BaseObject[]
     */
    public function visitContents(Structure\Contents $structure, File $file, Page $page): array
    {
        /** @var BaseObject[] $baseObjects */
        $baseObjects = [];

        foreach ($structure->getContent() as $baseContent) {
            $baseObjects[] = $baseContent->accept($this->contentVisitor, $file, $page);
        }

        return $baseObjects;
    }

    /**
     * @param Structure\Font\Type1 $structure
     * @param File $file
     *
     * @return BaseObject
     */
    public function visitType1Font(Structure\Font\Type1 $structure, File $file): BaseObject
    {
        $dictionary = $file->addDictionaryObject();

        $dictionary->addTextEntry('Type', '/Font');
        $dictionary->addTextEntry('Subtype', '/Type1');
        $dictionary->addTextEntry('BaseFont', '/' . $structure->getBaseFont());

        return $dictionary;
    }

    /**
     * @param Structure\Image $structure
     * @param File $file
     *
     * @return BaseObject
     */
    public function visitImage(Structure\Image $structure, File $file): BaseObject
    {
        $stream = $file->addStreamObject($structure->getImageData(), StreamObject::CONTENT_TYPE_IMAGE);

        $dictionary = $stream->getMetaData();
        $dictionary->setTextEntry('Type', '/XObject');
        $dictionary->setTextEntry('Subtype', '/Image');
        $dictionary->setTextEntry('Width', $structure->getWidth());
        $dictionary->setTextEntry('Height', $structure->getHeight());
        $dictionary->setTextEntry('Filter', $structure->getFilter());
        $dictionary->setTextEntry('BitsPerComponent', 8);
        $dictionary->setTextEntry('ColorSpace', '/DeviceRGB');
        $dictionary->setTextEntry('Filter', '/DCTDecode');

        return $stream;
    }

    /**
     * @param Structure\Font\Structure\FontStream $structure
     * @param File $file
     *
     * @return StreamObject
     */
    public function visitFontStream(Structure\Font\Structure\FontStream $structure, File $file)
    {
        $stream = $file->addStreamObject($structure->getFontData(), StreamObject::CONTENT_TYPE_FONT);

        $dictionary = $stream->getMetaData();
        $dictionary->setTextEntry('Subtype', '/' . $structure->getSubtype());

        return $stream;
    }

    /**
     * @param FontDescriptor $structure
     * @param File $file
     *
     * @return DictionaryObject
     */
    public function visitFontDescriptor(FontDescriptor $structure, File $file)
    {
        $dictionary = $file->addDictionaryObject();

        $dictionary->addTextEntry('Type', '/FontDescriptor');
        $dictionary->addTextEntry('FontName', $structure->getFontName());
        $dictionary->addTextEntry('Flags', $structure->getFlags());
        $dictionary->addTextEntry('FontBBox', $structure->getFontBBox());
        $dictionary->addTextEntry('ItalicAngle', $structure->getItalicAngle());
        $dictionary->addTextEntry('Ascent', $structure->getAscent());
        $dictionary->addTextEntry('Decent', $structure->getDecent());
        $dictionary->addTextEntry('CapHeight', $structure->getCapHeight());
        $dictionary->addTextEntry('StemV', $structure->getStemV());

        if ($structure->getFontFile3() !== null) {
            $reference = $structure->getFontFile3()->accept($this, $file);
            $dictionary->addReferenceEntry('FontFile3', $reference);
        }

        return $dictionary;
    }

    /**
     * @param Structure\Font\Type0 $structure
     * @param File $file
     *
     * @return DictionaryObject
     */
    public function visitType0Font(Structure\Font\Type0 $structure, File $file)
    {
        $dictionary = $file->addDictionaryObject();

        $dictionary->addTextEntry('Type', '/Font');
        $dictionary->addTextEntry('Subtype', '/Type0');
        $dictionary->addTextEntry('BaseFont', '/' . $structure->getBaseFont());

        $encoding = $structure->getEncoding()->accept($this, $file);
        $dictionary->addReferenceEntry('Encoding', $encoding);

        $descendantFont = $structure->getDescendantFont()->accept($this, $file);
        $dictionary->addReferenceArrayEntry('DescendantFonts', [$descendantFont]);

        $toUnicode = $structure->getToUnicode()->accept($this, $file);
        $dictionary->addReferenceEntry('ToUnicode', $toUnicode);

        return $dictionary;
    }

    /**
     * @param Structure\Font\Structure\CIDFont $structure
     * @param File $file
     *
     * @return DictionaryObject
     */
    public function visitCIDFont(Structure\Font\Structure\CIDFont $structure, File $file)
    {
        $dictionary = $file->addDictionaryObject();

        $cidDictionary = $structure->getCIDSystemInfo()->accept($this);
        $dictionary->addDictionaryEntry('CIDSystemInfo', $cidDictionary);

        $reference = $structure->getFontDescriptor()->accept($this, $file);
        $dictionary->addReferenceEntry('FontDescriptor', $reference);

        $dictionary->addNumberEntry('DW', $structure->getDW());
        $dictionary->addNumberArrayEntry('W', $structure->getW());

        return $dictionary;
    }

    /**
     * @param CMap $structure
     * @param File $file
     *
     * @return StreamObject
     */
    public function visitCMap(CMap $structure, File $file)
    {
        $stream = $file->addStreamObject($structure->getCMapData(), StreamObject::CONTENT_TYPE_TEXT);

        $dictionary = $stream->getMetaData();
        $dictionary->setTextEntry('Type', '/CMap');
        $dictionary->setTextEntry('CMapName', $structure->getCMapName());

        $cidDictionary = $structure->getCIDSystemInfo()->accept($this);
        $dictionary->setEntry('CIDSystemInfo', $cidDictionary);

        return $stream;
    }

    /**
     * @param CIDSystemInfo $structure
     *
     * @return DictionaryToken
     */
    public function visitCIDSystemInfo(CIDSystemInfo $structure)
    {
        $cidDictionary = new DictionaryToken();

        $cidDictionary->setTextEntry('Registry', $structure->getRegistry());
        $cidDictionary->setTextEntry('Ordering', $structure->getOrdering());
        $cidDictionary->setNumberEntry('Supplement', $structure->getSupplement());

        return $cidDictionary;
    }
}
