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

class StatefulPrinter
{
    /**
     * @var int
     */
    protected $page = 1;

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
     * @param int $page
     */
    public function setPage(int $page)
    {
        $this->page = $page;
    }

    /**
     * @param array $config
     * @param bool $restoreDefaults
     *
     * @throws \Exception
     */
    public function configure(array $config, bool $restoreDefaults = true)
    {
        $this->configurationChanged = true;

        if ($restoreDefaults) {
            $this->configuration = new PrintConfiguration();
        }

        $this->configuration->setConfiguration($config);
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
