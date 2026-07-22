<?php

namespace Famoser\PdfGenerator\Utils;

trait SingletonTrait
{
    private static ?self $instance = null;

    public static function instance(): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
