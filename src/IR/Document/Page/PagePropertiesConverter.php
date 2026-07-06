<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\IR\Document\Page;

class PagePropertiesConverter
{
    private const PROPERTIES_DEFAULTS = [
        'Rotate' => 0,
        'UserUnit' => 1.0,
    ];

    /**
     * @param PageProperties $pageProperties
     * @return array<string, mixed>
     */
    public static function convert(PageProperties $pageProperties): array
    {
        $properties = array_filter([
            // no media box, as this is a required property handled separately
            'CropBox' => $pageProperties->getCropBox(),
            'BleedBox' => $pageProperties->getBleedBox(),
            'TrimBox' => $pageProperties->getTrimBox(),
            'ArtBox' => $pageProperties->getArtBox(),
            'Dur' => $pageProperties->getShowDuration(),
            'Rotate' => $pageProperties->getRotate(),
            'UserUnit' => $pageProperties->getUserUnit(),
        ]);
        foreach (self::PROPERTIES_DEFAULTS as $key => $default) {
            if (array_key_exists($key, $properties) && $properties[$key] === $default) {
                unset($properties[$key]);
            }
        }

        $transitionProperties = self::convertTransitionProperties($pageProperties);
        if (null !== $transitionProperties) {
            $properties['Trans'] = $transitionProperties;
        }

        return $properties;
    }

    private const TRANSITION_PROPERTIES_DEFAULTS = [
        'S' => 'R',
        'D' => 1,
        'Dm' => 'H',
        'M' => 'I',
        'Di' => 0,
        'SS' => 1.0,
        'B' => false,
    ];

    private static function convertTransitionProperties(PageProperties $pageProperties): ?array
    {
        $transitionProperties = array_filter([
            'S' => $pageProperties->getTransition(),
            'D' => $pageProperties->getTransitionDuration(),
            'Dm' => $pageProperties->getTransitionDimension(),
            'M' => $pageProperties->getTransitionMotion(),
            'Di' => $pageProperties->getTransitionDirection(),
            'SS' => $pageProperties->getTransitionScaleStartOrEnd(),
            'B' => $pageProperties->getTransitionRectangular(),
        ]);

        foreach (self::TRANSITION_PROPERTIES_DEFAULTS as $key => $default) {
            if (array_key_exists($key, $transitionProperties) && $transitionProperties[$key] === $default) {
                unset($transitionProperties[$key]);
            }
        }

        return count($transitionProperties) ? $transitionProperties : null;
    }
}
