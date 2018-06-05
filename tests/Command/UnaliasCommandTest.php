<?php

namespace Geekish\Crap\Command;

use Geekish\Crap\Crap;
use Geekish\Crap\CrapHelper;
use Geekish\Crap\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class UnaliasCommandTest
 * @package Geekish\Crap\Command
 */
class UnaliasCommandTest extends TestCase
{
    /** @var CrapHelper */
    private $helper;

    /** @var AliasCommand */
    private $command;

    protected function setUp()
    {
        $container = $this->createContainer();
        $helper = $container->get(CrapHelper::class);
        $helper->setFile($this->createFileStore($this->readFile));

        $command = new UnaliasCommand($helper);

        $crap = $container->get(Crap::class);
        $crap->add($command);

        $this->helper = $helper;
        $this->command = $crap->find($command->getName());
    }

    public function testNotDryRun()
    {
        $tester = new CommandTester($this->command);

        $alias = 'foo';
        $package = 'bar/baz';

        $this->helper->setAlias($alias, $package);

        $tester->execute([
            'command' => $this->command->getName(),
            'alias' => [$alias],
            '--dry-run' => false,
        ]);

        $this->assertFalse($this->helper->hasAlias($alias));
    }

    public function testInvalidAlias()
    {
        $tester = new CommandTester($this->command);

        $alias = 'nope';

        $tester->execute([
            'command' => $this->command->getName(),
            'alias' => [$alias],
        ]);

        $expects = sprintf('Alias `%s` does not exist. Skipping.', $alias);

        $this->assertEquals($expects, trim($tester->getDisplay()));
    }

    public function testManyAliases()
    {
        $file = $this->createFileStore($this->manyFile);
        $this->helper->setFile($file);

        $tester = new CommandTester($this->command);

        $aliases = ['laravel52', 'laravel53', 'phpunit5'];

        $tester->execute([
            'command' => $this->command->getName(),
            'alias' => $aliases,
        ]);

        $output = explode("\n", trim($tester->getDisplay()));

        foreach ($aliases as $index => $alias) {
            $expects = sprintf('<success>Alias `%s` successfully removed.</success>', $alias);

            $this->assertFalse($this->helper->hasAlias($alias));
            $this->assertEquals($expects, $output[$index]);
        }
    }
}
