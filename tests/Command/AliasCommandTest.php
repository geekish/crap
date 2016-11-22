<?php

namespace Geekish\Crap\Command;

use Geekish\Crap\Crap;
use Geekish\Crap\CrapHelper;
use Geekish\Crap\CrapException;
use Geekish\Crap\TestCase;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;

class AliasCommandTest extends TestCase
{
    /** @var CommandTester */
    private $tester;

    public function setUp()
    {
        $container = $this->createContainer();
        $helper = $container->get(CrapHelper::class);

        $crap = $container->get(Crap::class);
        $crap->add(new AliasCommand($helper));

        $command = $crap->find("alias");

        $this->tester = new CommandTester($command);
    }

    public function testNoArguments()
    {
        $this->expectException(RuntimeException::class);

        $tester = $this->tester;

        $tester->execute([
            "command" => "alias",
        ]);
    }

    public function testInvalidAlias()
    {
        $tester = $this->tester;

        $this->expectException(CrapException::class);

        $tester->execute([
            "command" => "alias",
            "alias" => "InVaLiDaLiAs",
            "package" => "phpunit/phpunit"
        ]);

        $this->assertEquals(1, $tester->getStatusCode());
    }

    public function testInvalidPackage()
    {
        $tester = $this->tester;

        $this->expectException(CrapException::class);

        $tester->execute([
            "command" => "alias",
            "alias" => "validalias",
            "package" => "InVaLiDpAcKaGe"
        ]);

        $this->assertEquals(1, $tester->getStatusCode());
    }
}
