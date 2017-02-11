<?php

namespace Geekish\Crap;

use Exception;

/**
 * Class CrapHelperTest
 * @package Geekish\Crap
 */
class CrapHelperTest extends TestCase
{
    /** @var CrapHelper */
    protected $helper;

    public function setUp()
    {
        $container = $this->createContainer();

        $helper = $container->get(CrapHelper::class);
        $helper->setFile($this->createFileStore($this->readFile));
        $this->helper = $helper;
    }

    public function testGetVersionParser()
    {
        $this->assertInstanceOf(
            "\\Composer\\Package\\Version\\VersionParser",
            $this->helper->getVersionParser()
        );
    }

    public function testGetFile()
    {
        $this->assertInstanceOf(
            "Webmozart\\KeyValueStore\\JsonFileStore",
            $this->helper->getFile()
        );
    }

    public function testSetFile()
    {
        $previous = $this->helper->getFile();

        $setFile = $this->createFileStore($this->setFile);

        $this->helper->setFile($setFile);

        $this->assertEquals($setFile, $this->helper->getFile());

        $this->helper->setFile($previous);
    }

    public function testGetAlias()
    {
        $actual = $this->helper->getAlias("phpunit");

        $this->assertEquals("phpunit/phpunit:^5.0", $actual);
    }

    public function testGetUndefinedAlias()
    {
        $this->assertNull($this->helper->getAlias("fake"));
    }

    public function testGetAliases()
    {
        $actual = $this->helper->getAliases();

        $this->assertEquals(["mockery", "monolog", "phpcs", "phpunit"], $actual);
    }

    public function testHasAlias()
    {
        $this->assertTrue($this->helper->hasAlias("phpunit"));
    }

    public function testDoesNotHaveFakeAlias()
    {
        $this->assertFalse($this->helper->hasAlias("fake"));
    }

    public function testSetAlias()
    {
        $this->helper->setAlias("foo", "bar/baz");
        $this->assertEquals("bar/baz", $this->helper->getAlias("foo"));
    }

    public function testUnsetAlias()
    {
        $this->helper->unsetAlias("foo");
        $this->assertFalse($this->helper->hasAlias("foo"));
    }

    public function testParseArguments()
    {
        $toParse = ["monolog", "phpunit"];
        $expect = ["monolog/monolog", "phpunit/phpunit:^5.0"];

        $this->assertEquals($expect, $this->helper->parseArguments($toParse));
    }

    public function testParseArgumentsWithUndefinedAlias()
    {
        $this->expectException(Exception::class);
        $this->helper->parseArguments(["fake", "phpunit"]);
    }

    public function testParseArgumentsWithFullPackage()
    {
        $toParse = ["monolog", "phpunit", "my/package"];
        $expect = ["monolog/monolog", "phpunit/phpunit:^5.0", "my/package"];

        $this->assertEquals($expect, $this->helper->parseArguments($toParse));
    }

    public function testValidateAlias()
    {
        $this->assertTrue($this->helper->validateAlias("alias"));
    }

    public function testValidateAliasInvalid()
    {
        $this->assertFalse($this->helper->validateAlias("this+is;invalid"));
    }

    public function testValidatePackage()
    {
        $this->assertTrue($this->helper->validatePackage("package/package:^1.0"));
    }

    public function testValidateEmptyPackage()
    {
        $this->assertFalse($this->helper->validatePackage(""));
    }

    public function testValidatePackageInvalidPackageName()
    {
        $this->assertFalse($this->helper->validatePackage("ThIsIsNoTvAlId"));
    }

    public function testValidatePackageInvalidVersion()
    {
        $this->assertFalse($this->helper->validatePackage("package/package:%%%"));
    }
}
