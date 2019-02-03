<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Printer;

use PdfGenerator\IR\Configuration\PrintConfiguration;
use PdfGenerator\IR\Cursor;

class StatefulPrinter
{
    /**
     * @var Cursor
     */
    protected $cursor;

    /**
     * @var PrintConfiguration
     */
    protected $configuration;

    /**
     * @var bool
     */
    private $configurationChanged = true;

    /**
     * StatefulPrinter constructor.
     */
    public function __construct()
    {
        $this->configuration = new PrintConfiguration();
    }

    /**
     * returns the active cursor position.
     *
     * @return Cursor
     */
    public function getCursor()
    {
        return $this->cursor;
    }

    /**x
     * @param Cursor $cursor
     */
    public function setCursor(Cursor $cursor)
    {
        $this->cursor = $cursor;
    }

    /**
     * @param array $config
     * @param bool $restoreDefaults
     *
     * @throws \Exception
     */
    public function configure(array $config = [], bool $restoreDefaults = true)
    {
        $this->configurationChanged = true;

        if ($restoreDefaults) {
            $this->configuration = new PrintConfiguration();
        }

        $this->configuration->setConfiguration($config);
    }

    /**
     * @param PrintConfiguration $printConfiguration
     *
     * @throws \Exception
     */
    public function setConfiguration(PrintConfiguration $printConfiguration)
    {
        $this->configuration = PrintConfiguration::createFromExisting($printConfiguration);
        $this->configurationChanged = true;
    }

    /**
     * applies the config if it has changed.
     */
    protected function ensureConfigurationApplied()
    {
        if (!$this->configurationChanged) {
            return;
        }

        $this->configurationChanged = false;
    }
}
