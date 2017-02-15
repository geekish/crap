<?php

namespace Geekish\Crap;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Crap
 * @package Geekish\Crap
 * @codeCoverageIgnore
 */
final class Crap extends Application
{
    const FILENAME = "crap.json";

    /**
     * Crap application version
     * @var string
     */
    const VERSION = "1.0.0";

    private static $logo = '   __________  ___    ____
  / ____/ __ \/   |  / __ \
 / /   / /_/ / /| | / /_/ /
/ /___/ _, _/ ___ |/ ____/
\____/_/ |_/_/  |_/_/
';

    /**
     * Crap Constructor
     */
    public function __construct()
    {
        parent::__construct("crap", self::VERSION);
    }

    /**
     * @inheritDoc
     */
    public function getHelp()
    {
        return self::$logo . parent::getHelp();
    }

    /**
     * @inheritDoc
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        $this->setCatchExceptions(false);
        $this->setAutoExit(false);

        set_exception_handler(new ExceptionHandler($output));

        return $this->doRun($input, $output);
    }
}
