<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Configuration;

use Pdf\Configuration\PageConfiguration;

class DrawConfiguration extends PageConfiguration
{
    const FILL_COLOR = 'FILL_COLOR';
    const BORDER_COLOR = 'BORDER_COLOR';

    /**
     * @var string|null
     */
    private $fillColor = null;

    /**
     * @var string|null
     */
    private $borderColor = null;

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
        parent::__construct($configurationValidator);

        $this->configurationValidator = $configurationValidator;
    }

    /**
     * @param array $config
     *
     * @throws \Exception
     */
    public function setConfiguration(array $config)
    {
        parent::setConfiguration($config);

        if (isset($config[self::FILL_COLOR])) {
            $this->fillColor = $this->configurationValidator->color($config, self::FILL_COLOR);
        }

        if (isset($config[self::BORDER_COLOR])) {
            $this->borderColor = $this->configurationValidator->color($config, self::BORDER_COLOR);
        }
    }

    /**
     * @return string|null
     */
    protected function getFillColor(): ?string
    {
        return $this->fillColor;
    }

    /**
     * @return string|null
     */
    protected function getBorderColor(): ?string
    {
        return $this->borderColor;
    }

    /**
     * @return array
     */
    public function getConfiguration()
    {
        return parent::getConfiguration() + [
            self::FILL_COLOR => $this->getFillColor(),
            self::BORDER_COLOR => $this->getBorderColor(),
        ];
    }
}
