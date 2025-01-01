<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
