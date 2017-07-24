<?php

namespace Geekish\Crap\Command;

use Composer\Command\RemoveCommand as ComposerRemoveCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RemoveCommand
 * @package Geekish\Crap\Command
 */
final class RemoveCommand extends BaseComposerCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('remove');
        $this->setDescription('Gets package name and version by alias, calls `composer remove`');
        $this->addArgument('aliases', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Package aliases');

        $command = new ComposerRemoveCommand;

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
        $packages = $this->helper->parseArguments($input->getArgument('aliases'), true);

        $options = $this->getOptions($input, $output->isDecorated());
        $helper = $this->getHelper('process');
        $process = $this->createProcess('remove', $packages, $options);

        $helper->run($output, $process, 'Command failed.', function ($type, $data) use ($output) {
            $output->write($data, false);
        });

        return $process->getExitCode();
    }
}
