<?php

namespace Geekish\Crap;

use Exception;
use Throwable;
use Symfony\Component\Console\Output\OutputInterface;

class ExceptionHandler
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * Create ExceptionHandler
     *
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Handle the Exception/Throwable
     * We avoid type hinting to support both 5.6 & 7.x
     *
     * @param Exception|Throwable $e
     *
     * @param bool $exit
     * @return int|void
     */
    public function __invoke($e, $exit = true)
    {
        $this->output->writeln(sprintf("<error>%s</error>", $e->getMessage()));

        return $exit ? exit(1) : 1;
    }
}
