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
 * Class AliasCommandTest
 * @package Geekish\Crap\Command
 */
class AliasCommandTest extends TestCase
{
    /** @var CrapHelper */
    private $helper;

    /** @var AliasCommand */
    private $command;

    protected function setUp()
    {
        $container = $this->createContainer();
        $helper = $container->get(CrapHelper::class);
        $helper->setFile($this->createFileStore($this->writeFile));

        $crap = $container->get(Crap::class);
        $crap->add(new AliasCommand($helper));

        $command = $crap->find('alias');

        $this->helper = $helper;
        $this->command = $command;
    }

    public function testNoArguments()
    {
        $this->expectException(RuntimeException::class);

        $tester = new CommandTester($this->command);

        $tester->execute([
            'command' => $this->command->getName(),
        ]);
    }

    public function testInvalidAlias()
    {
        $tester = new CommandTester($this->command);

        $this->expectException(CrapException::class);

        $tester->execute([
            'command' => $this->command->getName(),
            'alias' => 'InVaLiDaLiAs',
            'package' => 'phpunit/phpunit'
        ]);

        $this->assertEquals(1, $tester->getStatusCode());
    }

    public function testInvalidPackage()
    {
        $tester = new CommandTester($this->command);

        $this->expectException(CrapException::class);

        $tester->execute([
            'command' => $this->command->getName(),
            'alias' => 'validalias',
            'package' => 'InVaLiDpAcKaGe'
        ]);

        $this->assertEquals(1, $tester->getStatusCode());
    }

    public function testInteractMissingAlias()
    {
        $alias = 'phpunit';
        $package = 'phpunit/phpunit';

        $question = $this->getMockBuilder(QuestionHelper::class)
            ->setMethods(['ask'])
            ->getMock();

        $question->expects($this->at(0))
            ->method('ask')
            ->will($this->returnValue($alias));

        $command = $this->command;
        $command->getHelperSet()->set($question, 'question');

        $tester = new CommandTester($command);

        $tester->execute([
            'command' => $this->command->getName(),
            'alias' => $package,
            'package' => null,
            '--dry-run' => true,
        ], [
            'interactive' => true,
        ]);

        $messages = explode("\n", trim($tester->getDisplay()));

        $expects = 'You provided the package but no alias!';

        $this->assertEquals($expects, $messages[0]);

        $expects = sprintf(
            '<success>Alias `%s` to package `%s` successfully added.</success>',
            $alias,
            $package
        );

        $this->assertEquals($expects, $messages[1]);
    }

    public function testInteractMissingPackage()
    {
        $alias = 'phpunit';
        $package = 'phpunit/phpunit';

        $question = $this->getMockBuilder(QuestionHelper::class)
            ->setMethods(['ask'])
            ->getMock();

        $question->expects($this->at(0))
            ->method('ask')
            ->will($this->returnValue($package));

        $command = $this->command;
        $command->getHelperSet()->set($question, 'question');

        $tester = new CommandTester($command);

        $tester->execute([
            'command' => $this->command->getName(),
            'alias' => $alias,
            'package' => null,
            '--dry-run' => true,
        ], [
            'interactive' => true,
        ]);

        $messages = explode("\n", trim($tester->getDisplay()));

        $expects = 'You provided the alias but no package!';

        $this->assertEquals($expects, $messages[0]);

        $expects = sprintf(
            '<success>Alias `%s` to package `%s` successfully added.</success>',
            $alias,
            $package
        );

        $this->assertEquals($expects, $messages[1]);
    }

    public function testInteractSwappedArguments()
    {
        $alias = 'phpunit';
        $package = 'phpunit/phpunit';

        $question = $this->getMockBuilder(QuestionHelper::class)
            ->setMethods(['ask'])
            ->getMock();

        $question->expects($this->at(0))
            ->method('ask')
            ->will($this->returnValue(true));

        $command = $this->command;
        $command->getHelperSet()->set($question, 'question');

        $tester = new CommandTester($command);

        $tester->execute([
            'command' => $this->command->getName(),
            'alias' => $package,
            'package' => $alias,
            '--dry-run' => true,
        ], [
            'interactive' => true,
        ]);

        $messages = explode("\n", trim($tester->getDisplay()));

        $expects = 'It looks like you swapped the package and alias.';

        $this->assertEquals($expects, $messages[0]);

        $expects = sprintf(
            '<success>Alias `%s` to package `%s` successfully added.</success>',
            $alias,
            $package
        );

        $this->assertEquals($expects, $messages[1]);
    }

    public function testInteractAliasToExistingAlias()
    {
        $alias = 'foo';
        $existing = 'alias';
        $package = $this->helper->getAlias($existing);

        $question = $this->getMockBuilder(QuestionHelper::class)
            ->setMethods(['ask'])
            ->getMock();

        $question->expects($this->at(0))
            ->method('ask')
            ->will($this->returnValue(true));

        $command = $this->command;
        $command->getHelperSet()->set($question, 'question');

        $tester = new CommandTester($command);

        $tester->execute([
            'command' => $this->command->getName(),
            'alias' => $alias,
            'package' => $existing,
            '--dry-run' => true,
        ], [
            'interactive' => true,
        ]);

        $messages = explode("\n", trim($tester->getDisplay()));

        $expects = 'You provided an existing alias instead of a package.';

        $this->assertEquals($expects, $messages[0]);

        $expects = sprintf(
            '<success>Alias `%s` to package `%s` successfully added.</success>',
            $alias,
            $package
        );

        $this->assertEquals($expects, $messages[1]);
    }

    public function testExistingExactMatch()
    {
        $tester = new CommandTester($this->command);

        $alias = 'alias';
        $package = 'package/package';

        $tester->execute([
            'command' => $this->command->getName(),
            'alias' => $alias,
            'package' => $package
        ]);

        $message = trim($tester->getDisplay());
        $expects = sprintf('Alias `%s` to package `%s` already exists, silly.', $alias, $package);

        $this->assertEquals($expects, $message);
    }

    public function testExistingOverrideFalse()
    {
        $alias = 'alias';
        $package = 'package/newpackage';
        $existing = 'package/package';

        $question = $this->getMockBuilder(QuestionHelper::class)
            ->setMethods(['ask'])
            ->getMock();

        $question->expects($this->at(0))
            ->method('ask')
            ->will($this->returnValue(false));

        $command = $this->command;
        $command->getHelperSet()->set($question, 'question');

        $tester = new CommandTester($command);

        $tester->execute([
            'command' => $command->getName(),
            'alias' => $alias,
            'package' => $package,
            '--dry-run' => true,
        ]);

        $message = trim($tester->getDisplay());
        $expects = sprintf('Alias `%s` exists and is set to `%s`.', $alias, $existing);

        $this->assertEquals($expects, $message);
    }

    public function testExistingOverrideTrue()
    {
        $alias = 'alias';
        $package = 'package/newpackage';
        $existing = 'package/package';

        $question = $this->getMockBuilder(QuestionHelper::class)
            ->setMethods(['ask'])
            ->getMock();

        $question->expects($this->at(0))
            ->method('ask')
            ->will($this->returnValue(true));

        $command = $this->command;
        $command->getHelperSet()->set($question, 'question');

        $tester = new CommandTester($command);

        $tester->execute([
            'command' => $command->getName(),
            'alias' => $alias,
            'package' => $package,
            '--dry-run' => true,
        ]);

        $messages = explode("\n", $tester->getDisplay());

        $expects = sprintf(
            '<success>Alias `%s` to package `%s` successfully %s.</success>',
            $alias,
            $package,
            'updated'
        );

        $this->assertEquals($expects, $messages[1]);
    }

    public function testNotDryRun()
    {
        $tester = new CommandTester($this->command);

        $alias = 'foo';
        $package = 'bar/baz';

        $tester->execute([
            'command' => $this->command->getName(),
            'alias' => $alias,
            'package' => $package,
            '--dry-run' => false,
        ]);

        $actual = $this->helper->getAlias($alias);

        $this->helper->unsetAlias($alias);

        $this->assertEquals($package, $actual);
    }
}
