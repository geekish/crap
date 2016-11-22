<?php

namespace Geekish\Crap\Command;

use Geekish\Crap\CrapException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Class AliasCommand
 * @package Geekish\Crap\Command
 */
final class AliasCommand extends BaseCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName("alias");
        $this->setAliases(["define"]);
        $this->setDescription("Defines an alias for a package to be used by crap.");
        $this->addArgument("alias", InputArgument::REQUIRED, "Package alias");
        $this->addArgument("package", InputArgument::REQUIRED, "Package");
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $alias = $input->getArgument("alias");
        $package = $input->getArgument("package");

        if (!$this->helper->validateAlias($alias)) {
            throw CrapException::create(
                "The alias `%s` is invalid, it should be lowercase, and match: [a-z0-9_.-]+",
                $alias
            );
        }

        if (!$this->helper->validatePackage($package)) {
            throw CrapException::create(
                "The package `%s` is invalid, it should match: [a-z0-9_.-]+/[a-z0-9_.-]+",
                $input->getArgument("package")
            );
        }

        if ($this->helper->hasAlias($alias)) {
            $current = $this->helper->getAlias($alias);

            if ($current == $package) {
                $output->writeln(sprintf(
                    "<comment>Alias `%s` to package `%s` already exists, silly.</comment>",
                    $alias,
                    $package
                ));
                return 0;
            }

            $helper = $this->getHelper("question");

            $ask = sprintf("Replace existing alias to `%s` with `%s`? (y/n) ", $alias, $package);
            $question = new ConfirmationQuestion($ask, false);

            if (!$helper->ask($input, $output, $question)) {
                return 0;
            }
        }

        $this->helper->setAlias($alias, $package);

        $output->writeln(sprintf(
            "<success>Alias `%s` to package `%s` successfully added.</success>",
            $alias,
            $package
        ));

        return 0;
    }
}
