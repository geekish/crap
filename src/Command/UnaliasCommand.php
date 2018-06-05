<?php

namespace Geekish\Crap\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UnaliasCommand
 * @package Geekish\Crap\Command
 */
final class UnaliasCommand extends BaseCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('unalias');
        $this->setDescription('Unset an existing crap alias.');
        $this->addArgument('alias', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Package alias(es)');
        $this->addOption(
            'dry-run',
            null,
            InputOption::VALUE_NONE,
            'Run command without writing to your `crap.json`, useful for testing.'
        );
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $aliases = $input->getArgument('alias');

        foreach ($aliases as $alias) {
            if (!$this->helper->hasAlias($alias)) {
                $output->writeln(sprintf(
                    '<comment>Alias `%s` does not exist. Skipping.</comment>',
                    $alias
                ));
                continue;
            }

            if ($input->getOption('dry-run') !== true) {
                $this->helper->unsetAlias($alias);
            }

            $output->writeln(sprintf(
                '<success>Alias `%s` successfully removed.</success>',
                $alias
            ));
        }

        return 0;
    }
}
