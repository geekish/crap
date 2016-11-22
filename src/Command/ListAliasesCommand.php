<?php

namespace Geekish\Crap\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ListAliasesCommand
 * @package Geekish\Crap\Command
 */
final class ListAliasesCommand extends BaseCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName("aliases");
        $this->setAliases(["list-aliases"]);
        $this->setDescription("List currently defined aliases");
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $aliases = $this->helper->getAliases();

        if (count($aliases) > 0) {
            $pad = max(array_map("strlen", $aliases)) + 3;

            foreach ($aliases as $alias) {
                $package = $this->helper->getAlias($alias);
                $output->writeln(sprintf(
                    "<comment>%s</comment> %s",
                    str_pad($alias, $pad, " "),
                    $package
                ));
            }

            return 0;
        }

        $output->writeln("<comment>No aliases defined.</comment>");

        return 0;
    }
}
