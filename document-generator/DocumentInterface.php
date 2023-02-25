<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DocumentGenerator;

interface DocumentInterface
{
    /**
     * starts a region with columns.
     */
    public function createColumnLayout(int $columnCount);

    public function setMeta(string $title, string $author);

    public function save(string $filePath);
}
