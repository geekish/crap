<?php

namespace Geekish\Crap\Command;

use Geekish\Crap\CrapHelper;
use Symfony\Component\Console\Command\Command;

/**
 * Class BaseCommand
 * @package Geekish\Crap\Command
 */
abstract class BaseCommand extends Command
{
    /** @var CrapHelper */
    protected $helper;

    public function __construct(CrapHelper $helper)
    {
        $this->helper = $helper;

        parent::__construct();
    }
}
