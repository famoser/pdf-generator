<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Configuration;

use PdfGenerator\Backend\Content\Operators\Level\TextLevel;
use PdfGenerator\Backend\Content\Operators\State\GeneralGraphicState;

class PrintConfiguration extends DrawConfiguration
{
    const FONT_FAMILY = 'FONT_FAMILY';
    const FONT_SIZE = 'FONT_SIZE';

    /**
     * @var string
     */
    private $fontFamily = 'Helvetica';

    /**
     * @var float
     */
    private $fontSize = 8;

    /**
     * @var ConfigurationValidator
     */
    private $configurationValidator;

    /**
     * PrintConfiguration constructor.
     */
    public function __construct()
    {
        $validator = new ConfigurationValidator();
        parent::__construct($validator);

        $this->configurationValidator = $validator;
    }

    /**
     * @param self $existing
     *
     * @throws \Exception
     *
     * @return PrintConfiguration
     */
    public static function createFromExisting(self $existing)
    {
        $new = new self();
        $new->setConfiguration($existing->getConfiguration());

        return $new;
    }

    /**
     * @param array $config
     *
     * @throws \Exception
     */
    public function setConfiguration(array $config)
    {
        parent::setConfiguration($config);

        if (isset($config[self::FONT_FAMILY])) {
            $this->fontFamily = $this->configurationValidator->fontFamily($config, self::FONT_FAMILY);
        }

        if (isset($config[self::FONT_SIZE])) {
            $this->fontSize = $this->configurationValidator->fontSize($config, self::FONT_SIZE);
        }
    }

    /**
     * @return string|null
     */
    public function getFontFamily(): string
    {
        return $this->fontFamily;
    }

    /**
     * @return float
     */
    public function getFontSize(): float
    {
        return $this->fontSize;
    }

    /**
     * @return array
     */
    public function getConfiguration()
    {
        return parent::getConfiguration() + [
                self::FONT_FAMILY => $this->getFontFamily(),
                self::FONT_SIZE => $this->getFontSize()
            ];
    }

    private function getGeneralGraphicState()
    {
        $graphicsState = new GeneralGraphicState();
        $graphicsState->setCurrentTransformationMatrix()
    }

    /**
     * @return TextLevel
     */
    public function createTextLevel()
    {
        $textLevel = new TextLevel()
    }
}
