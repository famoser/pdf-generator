<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdf\IR\Structure\Builder\Base;

abstract class BaseBuilder
{
    /**
     * @var mixed
     */
    private $entity;

    /**
     * @param $fieldName
     *
     * @throws \Exception
     */
    protected function throwForMissingField($fieldName)
    {
        throw new \Exception('You must set ' . $fieldName . ' before you build the object.');
    }

    /**
     * @throws \Exception
     *
     * @return mixed
     */
    abstract protected function construct();

    /**
     * @throws \Exception
     *
     * @return mixed
     */
    public function build()
    {
        if ($this->entity === null) {
            $this->entity = $this->construct();
        }

        return $this->entity;
    }
}
