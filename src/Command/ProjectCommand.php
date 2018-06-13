<?php

namespace Geekish\Crap\Command;

use Composer\Command\CreateProjectCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ProjectCommand
 * @package Geekish\Crap\Command
 */
final class ProjectCommand extends BaseComposerCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('project');
        $this->setDescription('Gets package name and version by alias, calls `composer create-project`');

        $this->addArgument('alias', InputArgument::REQUIRED, 'Package alias');

        $command = new CreateProjectCommand;
        $definition = $command->getDefinition();

        $directory = $definition->getArgument('directory');
        $version = $definition->getArgument('version');

        $this->getDefinition()->addArgument($directory);
        $this->getDefinition()->addArgument($version);

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
        $alias = $input->getArgument('alias');
        $directory = $input->getArgument('directory');
        $version = $input->getArgument('version');

        $alias = is_null($version) ? $alias : $alias . ':' . $version;
        $package = $this->helper->parseArguments([$alias], true);

        $args = [$package[0], $directory, $version];

        $options = $this->getOptions($input, $output->isDecorated());
        $helper = $this->getHelper('process');
        $process = $this->createProcess('create-project', $args, $options);

        $helper->run($output, $process, 'Command failed.', function ($type, $data) use ($output) {
            $output->write($data, false);
        });

        return $process->getExitCode();
    }
}
