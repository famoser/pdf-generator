<?php

namespace Famoser\PdfGenerator\Frontend\Page;

use Famoser\PdfGenerator\Frontend\Page\Transition\Transition;
use Famoser\PdfGenerator\Frontend\Page\Transition\TransitionDimension;
use Famoser\PdfGenerator\Frontend\Page\Transition\TransitionDirection;
use Famoser\PdfGenerator\Frontend\Page\Transition\TransitionMotion;

class PagePropertiesConverter
{
    public static function convert(PageProperties $pageProperties): \Famoser\PdfGenerator\IR\Document\Page\PageProperties
    {
        $mediaBox = array_merge([0, 0], $pageProperties->getSize());

        return new \Famoser\PdfGenerator\IR\Document\Page\PageProperties(
            $mediaBox,
            $pageProperties->getCropBox(),
            $pageProperties->getBleedBox(),
            $pageProperties->getTrimBox(),
            $pageProperties->getArtBox(),
            self::convertRotation($pageProperties->getRotation()),
            self::convertUnit($pageProperties->getUnit()),
            $pageProperties->getShowDuration(),
            self::convertTransition($pageProperties->getTransition()),
            $pageProperties->getTransitionDuration(),
            self::convertTransitionDimension($pageProperties->getTransitionDimension()),
            self::convertTransitionMotion($pageProperties->getTransitionMotion()),
            self::convertTransitionDirection($pageProperties->getTransitionDirection()),
            $pageProperties->getTransitionScaleStartOrEnd(),
            $pageProperties->getTransitionRectangular()
        );
    }

    private static function convertRotation(Rotation $rotation): int
    {
        return match ($rotation) {
            Rotation::Upright => 0,
            Rotation::RotatedClockwise => 90,
            Rotation::UpsideDown => 180,
            Rotation::RotatedCounterclockwise => 270,
        };
    }

    private static function convertUnit(Unit $unit): float
    {
        return match ($unit) {
            Unit::POINT => 1.0,
            Unit::INCHES => 72.0,
            Unit::MILLIMETERS => 72.0 / 25.4,
        };
    }

    private static function convertTransition(?Transition $transition): ?string
    {
        if (null === $transition) {
            return null;
        }

        return match ($transition) {
            Transition::Split => 'Split',
            Transition::Blinds => 'Blinds',
            Transition::Box => 'Box',
            Transition::Wipe => 'Wipe',
            Transition::Dissolve => 'Dissolve',
            Transition::Glitter => 'Glitter',
            Transition::Fly => 'Fly',
            Transition::Push => 'Push',
            Transition::Cover => 'Cover',
            Transition::Uncover => 'Uncover',
            Transition::Fade => 'Fade',
        };
    }

    private static function convertTransitionDimension(?TransitionDimension $dimension): ?string
    {
        if (null === $dimension) {
            return null;
        }

        return match ($dimension) {
            TransitionDimension::Horizontal => 'H',
            TransitionDimension::Vertical => 'V',
        };
    }

    private static function convertTransitionMotion(?TransitionMotion $motion): ?string
    {
        if (null === $motion) {
            return null;
        }

        return match ($motion) {
            TransitionMotion::Inward => 'I',
            TransitionMotion::Outward => 'O',
        };
    }

    private static function convertTransitionDirection(?TransitionDirection $direction): string|int|null
    {
        if (null === $direction) {
            return null;
        }

        return match ($direction) {
            TransitionDirection::LeftToRight => 0,
            TransitionDirection::BottomToTop => 90,
            TransitionDirection::RightToLeft => 180,
            TransitionDirection::TopToBottom => 270,
            TransitionDirection::TopLeftToBottomRight => 315,
            TransitionDirection::None => 'None',
        };
    }
}
