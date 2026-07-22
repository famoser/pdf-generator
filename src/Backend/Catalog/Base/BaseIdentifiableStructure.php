<?php

namespace Famoser\PdfGenerator\Backend\Catalog\Base;

abstract readonly class BaseIdentifiableStructure extends BaseStructure
{
    /**
     * @param array<string, mixed> $objectValues
     */
    public function __construct(private string $identifier, array $objectValues = [])
    {
        parent::__construct($objectValues);
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}
