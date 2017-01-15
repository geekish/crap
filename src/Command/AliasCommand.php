<?php

namespace Geekish\Crap\Command;

use Geekish\Crap\CrapException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

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
        $this->setDescription("Defines an alias for a package to be used by crap.");
        $this->addArgument("alias", InputArgument::REQUIRED, "Package alias");
        $this->addArgument("package", InputArgument::REQUIRED, "Package");
        $this->addOption(
            "dry-run",
            null,
            InputOption::VALUE_NONE,
            "Run command without writing to your `crap.json`, useful for testing."
        );
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

        $override = false;

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

            $output->writeln(sprintf(
                "<comment>Alias `%s` exists and is set to `%s`.</comment>",
                $alias,
                $current
            ));

            $ask = sprintf("Override alias `%s` with `%s`? (y/n) ", $alias, $package);
            $question = new ConfirmationQuestion($ask, false);

            if (!$helper->ask($input, $output, $question)) {
                return 0;
            }

            $override = true;
        }

        if ($input->getOption("dry-run") !== true) {
            $this->helper->setAlias($alias, $package);
        }

        $output->writeln(sprintf(
            "<success>Alias `%s` to package `%s` successfully %s.</success>",
            $alias,
            $package,
            $override ? "updated" : "added"
        ));

        return 0;
    }

    /**
     * @inheritDoc
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $args = array_values($input->getArguments());

        $helper = $this->getHelper("question");

        if ($args[2] == null && $this->helper->validatePackage($args[1]) === true) {
            $package = $args[1];

            $output->writeln("<comment>You provided the package but no alias!</comment>");

            $message = sprintf("<question>What do you want to use an an alias for `%s`?</question>", $package);
            $question = new Question($message . PHP_EOL, false);

            $alias = $helper->ask($input, $output, $question);

            $input->setArgument("alias", $alias);
            $input->setArgument("package", $package);
        } elseif ($args[2] == null && $this->helper->validateAlias($args[1])) {
            $alias = $args[1];

            $output->writeln("<info>You provided the alias but no package!</info>");

            $message = sprintf("<question>What package do you want to alias `%s` to?</question>", $alias);
            $question = new Question($message . PHP_EOL, false);

            $package = $helper->ask($input, $output, $question);

            $input->setArgument("package", $package);
        } elseif ($this->helper->validateAlias($args[2]) && $this->helper->validatePackage($args[1])) {
            $output->writeln("<info>It looks like you swapped the package and alias.</info>");

            $message = sprintf(
                "<question>Did you mean to alias `%s` to package `%s`?</question> (y/n) ",
                $args[2],
                $args[1]
            );

            $question = new ConfirmationQuestion($message, false);

            if ($helper->ask($input, $output, $question)) {
                $input->setArgument("alias", $args[2]);
                $input->setArgument("package", $args[1]);
            }
        } elseif ($this->helper->validateAlias($args[1]) && $this->helper->hasAlias($args[2])) {
            $output->writeln("<info>You provided an existing alias instead of a package.</info>");

            $existing = $this->helper->getAlias($args[2]);

            $message = sprintf(
                "<question>Do you want to alias `%s` to `%s`?</question> (y/n) ",
                $args[1],
                $existing
            );

            $question = new ConfirmationQuestion($message, false);

            if ($helper->ask($input, $output, $question)) {
                $input->setArgument("package", $existing);
            }
        }
    }
}
