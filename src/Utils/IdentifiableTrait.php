<?php


namespace PdfGenerator\Utils;


trait IdentifiableTrait
{
    /**
     * @var int
     */
    private static $nextIdentifier = 0;

    /**
     * @var int
     */
    private $identifier = self::$nextIdentifier++;

    /**
     * @return int
     */
    public function getIdentifier(): int
    {
        return $this->identifier;
    }
}