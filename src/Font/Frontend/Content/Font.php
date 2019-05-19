<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend\Content;

class Font
{
    /**
     * @var TableDirectory
     */
    private $tableDirectory;

    /**
     * @return TableDirectory
     */
    public function getTableDirectory(): TableDirectory
    {
        return $this->tableDirectory;
    }

    /**
     * @param TableDirectory $tableDirectory
     */
    public function setTableDirectory(TableDirectory $tableDirectory): void
    {
        $this->tableDirectory = $tableDirectory;
    }
}
