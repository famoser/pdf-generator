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

use PdfGenerator\Backend\Catalog\Base\BaseIdentifiableStructure;
use PdfGenerator\Backend\Catalog\Font\Structure\CIDSystemInfo;
use PdfGenerator\Backend\Catalog\Font\Structure\CMap;
use PdfGenerator\Backend\Catalog\Font\Structure\FontDescriptor;
use PdfGenerator\Backend\Catalog\Page;
use PdfGenerator\Backend\File\File;
use PdfGenerator\Backend\File\Object\Base\BaseObject;
use PdfGenerator\Backend\File\Object\DictionaryObject;
use PdfGenerator\Backend\File\Object\StreamObject;
use PdfGenerator\Backend\File\Token\DictionaryToken;

class CatalogVisitor
{
    /**
     * @var BaseObject[]
     */
    private array $objectNodeLookup = [];

    private File $file;

    /**
     * StructureVisitor constructor.
     */
    public function __construct(File $file)
    {
        $this->file = $file;
    }

    public function visitCatalog(Catalog\Catalog $structure): BaseObject
    {
        $dictionary = $this->file->addDictionaryObject();
        $dictionary->addNameEntry('Type', 'Catalog');

        $reference = $structure->getPages()->accept($this);
        $dictionary->addReferenceEntry('Pages', $reference);

        $dictionary = $this->file->addInfoDictionaryObject();
        $dictionary->addTextEntry('Creator', 'famoser/pdf-generator');
        $dictionary->addDateEntry('CreationDate', new \DateTime());

        return $dictionary;
    }

    public function visitPages(Catalog\Pages $structure): BaseObject
    {
        $dictionary = $this->file->addDictionaryObject();
        $dictionary->addNameEntry('Type', 'Pages');
        $this->objectNodeLookup[spl_object_id($structure)] = $dictionary;

        /** @var Page[] $kids */
        $kids = [];
        foreach ($structure->getKids() as $kid) {
            $kids[] = $kid->accept($this);
        }

        $dictionary->addReferenceArrayEntry('Kids', $kids);
        $dictionary->addNumberEntry('Count', \count($kids));

        return $dictionary;
    }

    public function visitPage(Page $structure): BaseObject
    {
        $dictionary = $this->file->addDictionaryObject();
        $dictionary->addNameEntry('Type', 'Page');

        $parentReference = $this->objectNodeLookup[spl_object_id($structure->getParent())];
        $dictionary->addReferenceEntry('Parent', $parentReference);

        $resources = $structure->getResources()->accept($this);
        $dictionary->addReferenceEntry('Resources', $resources);

        $dictionary->addNumberArrayEntry('MediaBox', $structure->getMediaBox());

        $contents = $structure->getContents()->accept($this);
        $dictionary->addReferenceArrayEntry('Contents', $contents);

        return $dictionary;
    }

    public function visitResources(Catalog\Resources $structure): BaseObject
    {
        $dictionary = $this->file->addDictionaryObject();

        // see PDF320000_2008 14.2
        $procSet = ['PDF'];

        if (\count($structure->getFonts()) > 0) {
            $fontDictionary = $this->createReferenceDictionary($structure->getFonts());
            $dictionary->addDictionaryEntry('Font', $fontDictionary);
            $procSet[] = 'Text';
        }

        if (\count($structure->getImages()) > 0) {
            $fontDictionary = $this->createReferenceDictionary($structure->getImages());
            $dictionary->addDictionaryEntry('XObject', $fontDictionary);
            $procSet[] = 'ImageC';
        }

        $dictionary->addNameArrayEntry('ProcSet', $procSet);

        return $dictionary;
    }

    /**
     * @var BaseObject[]
     */
    private array $referenceLookup = [];

    /**
     * @param BaseIdentifiableStructure[] $structures
     */
    private function createReferenceDictionary(array $structures): DictionaryToken
    {
        $dictionary = new DictionaryToken();
        foreach ($structures as $structure) {
            $identifier = $structure->getIdentifier();

            if (!\array_key_exists($identifier, $this->referenceLookup)) {
                $reference = $structure->accept($this);
                $this->referenceLookup[$identifier] = $reference;
            }

            $referenceToken = $this->referenceLookup[$identifier];
            $dictionary->setReferenceEntry($structure->getIdentifier(), $referenceToken);
        }

        return $dictionary;
    }

    /**
     * @return BaseObject[]
     */
    public function visitContents(Catalog\Contents $structure): array
    {
        /** @var BaseObject[] $baseObjects */
        $baseObjects = [];
        foreach ($structure->getContent() as $baseContent) {
            $baseObjects[] = $baseContent->accept($this);
        }

        return $baseObjects;
    }

    public function visitType1Font(Catalog\Font\Type1 $structure): BaseObject
    {
        $dictionary = $this->file->addDictionaryObject();

        $dictionary->addNameEntry('Type', 'Font');
        $dictionary->addNameEntry('Subtype', 'Type1');
        $dictionary->addNameEntry('BaseFont', $structure->getBaseFont());
        $dictionary->addNameEntry('Encoding', $structure->getEncoding());

        return $dictionary;
    }

    public function visitTrueTypeFont(Catalog\Font\TrueType $structure): DictionaryObject
    {
        $dictionary = $this->file->addDictionaryObject();

        $dictionary->addNameEntry('Type', 'Font');
        $dictionary->addNameEntry('Subtype', 'TrueType');
        $dictionary->addNameEntry('BaseFont', $structure->getBaseFont());
        $dictionary->addNameEntry('Encoding', $structure->getEncoding());

        $dictionary->addNumberEntry('FirstChar', 0);
        $dictionary->addNumberEntry('LastChar', 255);

        $dictionary->addNumberArrayEntry('Widths', $structure->getWidths());

        $reference = $structure->getFontDescriptor()->accept($this);
        $dictionary->addReferenceEntry('FontDescriptor', $reference);

        return $dictionary;
    }

    public function visitImage(Catalog\Image $structure): BaseObject
    {
        $stream = $this->file->addStreamObject($structure->getContent());

        $dictionary = $stream->getMetaData();
        $dictionary->setNameEntry('Type', 'XObject');
        $dictionary->setNameEntry('Subtype', 'Image');
        $dictionary->setNumberEntry('Width', $structure->getWidth());
        $dictionary->setNumberEntry('Height', $structure->getHeight());
        $dictionary->setNameEntry('Filter', $structure->getFilter());
        $dictionary->setNumberEntry('BitsPerComponent', 8);
        $dictionary->setNameEntry('ColorSpace', 'DeviceRGB');
        $dictionary->setNameEntry('Filter', 'DCTDecode');

        return $stream;
    }

    public function visitFontStream(Catalog\Font\Structure\FontStream $structure): StreamObject
    {
        $stream = $this->file->addStreamObject($structure->getFontData());

        $dictionary = $stream->getMetaData();
        $dictionary->setNameEntry('Subtype', $structure->getSubtype());

        return $stream;
    }

    public function visitFontDescriptor(FontDescriptor $structure): DictionaryObject
    {
        $dictionary = $this->file->addDictionaryObject();

        $dictionary->addNameEntry('Type', 'FontDescriptor');
        $dictionary->addNameEntry('FontName', $structure->getFontName());
        $dictionary->addNumberEntry('Flags', $structure->getFlags());
        $dictionary->addNumberArrayEntry('FontBBox', $structure->getFontBBox());
        $dictionary->addNumberEntry('ItalicAngle', $structure->getItalicAngle());
        $dictionary->addNumberEntry('Ascent', $structure->getAscent());
        $dictionary->addNumberEntry('Descent', $structure->getDescent());
        $dictionary->addNumberEntry('CapHeight', $structure->getCapHeight());
        $dictionary->addNumberEntry('StemV', $structure->getStemV());

        if (null !== $structure->getFontFile3()) {
            $reference = $structure->getFontFile3()->accept($this);
            $dictionary->addReferenceEntry('FontFile3', $reference);
        }

        return $dictionary;
    }

    public function visitType0Font(Catalog\Font\Type0 $structure): DictionaryObject
    {
        $dictionary = $this->file->addDictionaryObject();

        $dictionary->addNameEntry('Type', 'Font');
        $dictionary->addNameEntry('Subtype', 'Type0');
        $dictionary->addNameEntry('BaseFont', $structure->getBaseFont());

        $encoding = $structure->getEncoding()->accept($this);
        $dictionary->addReferenceEntry('Encoding', $encoding);

        $descendantFont = $structure->getDescendantFont()->accept($this);
        $dictionary->addReferenceArrayEntry('DescendantFonts', [$descendantFont]);

        $toUnicode = $structure->getToUnicode()->accept($this);
        $dictionary->addReferenceEntry('ToUnicode', $toUnicode);

        return $dictionary;
    }

    public function visitCIDFont(Catalog\Font\Structure\CIDFont $structure): DictionaryObject
    {
        $dictionary = $this->file->addDictionaryObject();

        $dictionary->addNameEntry('Type', 'Font');
        $dictionary->addNameEntry('Subtype', 'CIDFontType2');
        $dictionary->addNameEntry('CIDToGIDMap', 'Identity');
        $dictionary->addNameEntry('BaseFont', $structure->getBaseFont());

        $cidDictionary = $structure->getCIDSystemInfo()->accept($this);
        $dictionary->addDictionaryEntry('CIDSystemInfo', $cidDictionary);

        $reference = $structure->getFontDescriptor()->accept($this);
        $dictionary->addReferenceEntry('FontDescriptor', $reference);

        $dictionary->addNumberEntry('DW', $structure->getDW());
        $dictionary->addNumberOfNumbersArrayEntry('W', $structure->getW());

        return $dictionary;
    }

    public function visitCMap(CMap $structure): StreamObject
    {
        $stream = $this->file->addStreamObject($structure->getCMapData());

        $dictionary = $stream->getMetaData();
        $dictionary->setNameEntry('Type', 'CMap');
        $dictionary->setNameEntry('CMapName', $structure->getCMapName());

        $cidDictionary = $structure->getCIDSystemInfo()->accept($this);
        $dictionary->setDictionaryEntry('CIDSystemInfo', $cidDictionary);

        return $stream;
    }

    public function visitCIDSystemInfo(CIDSystemInfo $structure): DictionaryToken
    {
        $cidDictionary = new DictionaryToken();

        $cidDictionary->setTextEntry('Registry', $structure->getRegistry());
        $cidDictionary->setTextEntry('Ordering', $structure->getOrdering());
        $cidDictionary->setNumberEntry('Supplement', $structure->getSupplement());

        return $cidDictionary;
    }

    public function visitContent(Catalog\Content $param): StreamObject
    {
        return $this->file->addStreamObject($param->getContent());
    }
}
