<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 2/3/19
 * Time: 5:13 PM
 */

namespace DocumentGenerator\Document;


interface ColumnedPdfDocument
{
    /**
     * ensures the next printed elements are printed in the specified column
     * will throw an exception if the column does not exist.
     *
     * @param int $column
     */
    public function setColumn(int $column);

    /**
     * ensures the next printed elements are printed in the column which has fewest content
     */
    public function setColumnWithFewestContent();
}