<?php

namespace Geekish\Crap\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Class BaseComposerCommand
 * @package Geekish\Crap\Command
 */
abstract class BaseComposerCommand extends BaseCommand
{
    /**
     * Return options that are NOT default
     * We don't want to call Composer commands with every possible option set...
     *
     * @param InputInterface $input
     * @param bool $decorated
     * @return array
     */
    protected function getOptions(InputInterface $input, $decorated = true)
    {
        $inputOptions = $input->getOptions();
        $options = [];

        foreach ($this->getDefinition()->getOptions() as $option) {
            $name = $option->getName();
            if ($inputOptions[$name] !== $option->getDefault()) {
                $options[] = $name;
            }
        }

        if ($decorated) {
            $options[] = "ansi";
        }

        return $options;
    }

    /**
     * Create Process for Composer command
     *
     * @param $command
     * @param array $packages
     * @param array $options
     * @return Process
     */
    protected function createProcess($command, array $packages, array $options)
    {
        $arguments = [];

        $arguments[] = $command;

        if (count($options) > 0) {
            $options = array_map(function ($value) {
                return str_pad($value, strlen($value) + 2, "-", STR_PAD_LEFT);
            }, $options);

            array_push($arguments, ...$options);
        }

        array_push($arguments, ...$packages);

        return (new ProcessBuilder)
            ->setPrefix("composer")
            ->setArguments($arguments)
            ->setTimeout(null);
            ->getProcess();
    }
}
