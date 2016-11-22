<?php

namespace Geekish\Crap;

use Composer\Package\Version\VersionParser;
use mindplay\unbox\ContainerFactory;
use mindplay\unbox\ProviderInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Webmozart\KeyValueStore\JsonFileStore;

class CrapProvider implements ProviderInterface
{
    /** @var string */
    private $composerHome;

    /**
     * Create CrapHelperFactory
     *
     * @param string $composerHome
     */
    public function __construct($composerHome)
    {
        $this->composerHome = $composerHome;
    }

    public function register(ContainerFactory $factory)
    {
        $composerHome = $this->composerHome;

        $factory->register(ArgvInput::class);
        $factory->register(ConsoleOutput::class);

        $factory->alias(InputInterface::class, ArgvInput::class);
        $factory->alias(OutputInterface::class, ConsoleOutput::class);

        $factory->configure(
            ConsoleOutput::class,
            function (ConsoleOutput $output) {
                $output->getFormatter()->setStyle("error", new OutputFormatterStyle("red", null, []));
                $output->getFormatter()->setStyle("success", new OutputFormatterStyle("green", null, []));
            }
        );

        $factory->set(
            Helper\HelperSet::class,
            new Helper\HelperSet([
                new Helper\FormatterHelper(),
                new Helper\DebugFormatterHelper(),
                new Helper\ProcessHelper(),
                new Helper\QuestionHelper(),
            ])
        );

        $factory->register(VersionParser::class);
        $factory->register(CrapHelper::class);
        $factory->register(Crap::class);

        $factory->register(
            JsonFileStore::class,
            function () use ($composerHome) {
                $file = sprintf("%s/%s", $composerHome, Crap::FILENAME);
                $flags = JsonFileStore::NO_SERIALIZE_STRINGS
                    | JsonFileStore::PRETTY_PRINT
                    | JsonFileStore::NO_ESCAPE_SLASH;
                return new JsonFileStore($file, $flags);
            }
        );

        $factory->configure(
            Crap::class,
            function (Crap $crap, CrapHelper $helper) {
                $crap->addCommands([
                    new Command\ListAliasesCommand($helper),
                    new Command\AliasCommand($helper),
                    new Command\UnaliasCommand($helper),
                    new Command\RequireCommand($helper),
                    new Command\UpdateCommand($helper),
                    new Command\RemoveCommand($helper),
                ]);
                return $crap;
            }
        );
    }
}
