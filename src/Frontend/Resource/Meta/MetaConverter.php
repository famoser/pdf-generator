<?php

namespace Famoser\PdfGenerator\Frontend\Resource\Meta;

use Famoser\PdfGenerator\Frontend\Resource\Meta;

class MetaConverter
{
    public static function convert(Meta $meta): \Famoser\PdfGenerator\IR\Meta
    {
        return new \Famoser\PdfGenerator\IR\Meta(
            $meta->getLanguage(),
            $meta->getOtherLanguages(),
            $meta->getTitle(),
            $meta->getTitleTranslations(),
            $meta->getDescription(),
            $meta->getDescriptionTranslations(),
            $meta->getCreators(),
            $meta->getContributors(),
            $meta->getPublishers(),
            $meta->getKeywords(),
            $meta->getDates(),
        );
    }
}
