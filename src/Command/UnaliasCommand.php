<?php

namespace Geekish\Crap\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
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
        $this->setName("unalias");
        $this->setDescription("Unset an existing crap alias.");
        $this->addArgument("alias", InputArgument::REQUIRED, "Package alias");
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $alias = $input->getArgument("alias");

        if ($this->helper->hasAlias($alias)) {
            $this->helper->unsetAlias($alias);

            $output->writeln(sprintf(
                "<success>Alias `%s` successfully removed.</success>",
                $alias
            ));

            return 0;
        }

        $output->writeln(sprintf(
            "<comment>Alias `%s` does not exist.</comment>",
            $alias
        ));

        return 1;
    }
}
