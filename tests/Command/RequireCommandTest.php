<?php

namespace Geekish\Crap\Command;

use Geekish\Crap\Crap;
use Geekish\Crap\CrapException;
use Geekish\Crap\CrapHelper;
use Geekish\Crap\TestCase;

use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class RequireCommandTest
 * @package Geekish\Crap\Command
 */
class RequireCommandTest extends TestCase
{
    /** @var CommandTester */
    private $tester;

    protected function setUp()
    {
        $container = $this->createContainer();
        $helper = $container->get(CrapHelper::class);

        $crap = $container->get(Crap::class);
        $crap->add(new RequireCommand($helper));

        $command = $crap->find("require");

        $this->tester = new CommandTester($command);
    }

    public function testRequireUndefinedAlias()
    {
        $tester = $this->tester;

        $this->expectException(CrapException::class);

        $tester->execute([
            "command" => "require",
            "aliases" => ["doesnotexist"]
        ]);

        $this->assertEquals(1, $tester->getStatusCode());
    }
}
