<?php

namespace Geekish\Crap\Command;

use Geekish\Crap\Crap;
use Geekish\Crap\CrapHelper;
use Geekish\Crap\CrapException;
use Geekish\Crap\TestCase;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class InfoCommandTest
 * @package Geekish\Crap\Command
 */
class InfoCommandTest extends TestCase
{
    /** @var CrapHelper */
    private $helper;

    /** @var AliasCommand */
    private $command;

    public function setUp()
    {
        $container = $this->createContainer();
        $helper = $container->get(CrapHelper::class);
        $helper->setFile($this->createFileStore($this->readFile));

        $crap = $container->get(Crap::class);
        $crap->add(new InfoCommand($helper));

        $command = $crap->find("info");

        $this->helper = $helper;
        $this->command = $command;
    }

    public function testInfo()
    {
        $tester = new CommandTester($this->command);

        $alias = "phpunit";
        $package = $this->helper->getAlias($alias);

        $tester->execute([
            "command" => $this->command->getName(),
            "alias" => "phpunit",
        ]);

        $this->assertEquals(0, $tester->getStatusCode());

        $expects = sprintf("Alias `%s` is set to: %s", $alias, $package);

        $this->assertEquals($expects, trim($tester->getDisplay()));
    }

    public function testNoArguments()
    {
        $this->expectException(RuntimeException::class);

        $tester = new CommandTester($this->command);

        $tester->execute([
            "command" => $this->command->getName(),
        ]);
    }

    public function testInvalidAlias()
    {
        $tester = new CommandTester($this->command);

        $tester->execute([
            "command" => $this->command->getName(),
            "alias" => "nope",
        ]);

        $this->assertEquals(1, $tester->getStatusCode());
    }
}
