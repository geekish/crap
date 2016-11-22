<?php

namespace Geekish\Crap\Command;

use Composer\Command\RequireCommand as ComposerRequireCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RequireCommand
 * @package Geekish\Crap\Command
 */
final class RequireCommand extends BaseComposerCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName("require");
        $this->setDescription("Gets package name and version by alias, calls `composer require`");
        $this->addArgument("aliases", InputArgument::IS_ARRAY | InputArgument::REQUIRED, "Package aliases");

        $command = new ComposerRequireCommand;

        foreach ($command->getDefinition()->getOptions() as $option) {
            $this->getDefinition()->addOption($option);
        }
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $packages = $this->helper->parseArguments($input->getArgument("aliases"));

        $options = $this->getOptions($input, $output->isDecorated());
        $helper = $this->getHelper("process");
        $process = $this->createProcess("require", $packages, $options);

        $helper->run($output, $process, "Command failed.", function ($type, $data) use ($output) {
            $output->write($data, false);
        });

        return $process->getExitCode();
    }
}
