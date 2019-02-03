<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdf\IR\Configuration;

use PdfGenerator\IR\Configuration\ConfigurationValidator;

class PageConfiguration
{
    const PAGE_HEIGHT = 'PAGE_HEIGHT';
    const PAGE_WIDTH = 'PAGE_WIDTH';

    /**
     * @var float
     */
    private $pageHeight = 297;

    /**
     * @var float
     */
    private $pageWidth = 210;

    /**
     * @var ConfigurationValidator
     */
    private $configurationValidator;

    /**
     * PrintConfiguration constructor.
     *
     * @param ConfigurationValidator $configurationValidator
     */
    public function __construct(ConfigurationValidator $configurationValidator)
    {
        $this->configurationValidator = $configurationValidator;
    }

    /**
     * @param array $config
     *
     * @throws \Exception
     */
    public function setConfiguration(array $config)
    {
        if (isset($config[self::PAGE_WIDTH])) {
            $this->pageWidth = $this->configurationValidator->size($config, self::PAGE_WIDTH);
        }

        if (isset($config[self::PAGE_HEIGHT])) {
            $this->pageHeight = $this->configurationValidator->size($config, self::PAGE_HEIGHT);
        }
    }

    /**
     * @return float
     */
    public function getPageHeight(): float
    {
        return $this->pageHeight;
    }

    /**
     * @return float
     */
    public function getPageWidth(): float
    {
        return $this->pageWidth;
    }

    /**
     * @return array
     */
    public function getConfiguration()
    {
        return [
            self::PAGE_WIDTH => $this->getPageWidth(),
            self::PAGE_HEIGHT => $this->getPageHeight(),
        ];
    }
}
