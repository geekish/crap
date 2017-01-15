<?php

namespace Geekish\Crap\Command;

use Composer\Command\UpdateCommand as ComposerUpdateCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpdateCommand
 * @package Geekish\Crap\Command
 */
final class UpdateCommand extends BaseComposerCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName("update");
        $this->setDescription("Gets package name and version by alias, calls `composer update`");
        $this->addArgument("aliases", InputArgument::IS_ARRAY | InputArgument::REQUIRED, "Package aliases");

        $command = new ComposerUpdateCommand;

        foreach ($command->getDefinition()->getOptions() as $option) {
            $this->getDefinition()->addOption($option);
        }
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $packages = $this->helper->parseArguments($input->getArgument("aliases"));

        $options = $this->getOptions($input, $output->isDecorated());
        $helper = $this->getHelper("process");
        $process = $this->createProcess("update", $packages, $options);

        $helper->run($output, $process, "Command failed.", function ($type, $data) use ($output) {
            $output->write($data, false);
        });

        return $process->getExitCode();
    }
}
