<?php

namespace Geekish\Crap\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UnaliasCommand
 * @package Geekish\Crap\Command
 */
final class InfoCommand extends BaseCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName("info");
        $this->setDescription("Get a single alias.");
        $this->addArgument("alias", InputArgument::REQUIRED, "Package alias");
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $alias = $input->getArgument("alias");

        if (!$this->helper->hasAlias($alias)) {
            $output->writeln(sprintf(
                "<success>Alias `%s` does not exist.</success>",
                $alias
            ));

            return 1;
        }

        $package = $this->helper->getAlias($alias);

        $output->writeln(sprintf("Alias `%s` is set to: <comment>%s</comment>", $alias, $package));

        return 0;
    }
}
