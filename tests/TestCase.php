<?php

namespace Geekish\Crap;

use mindplay\unbox\ContainerFactory;
use Webmozart\KeyValueStore\JsonFileStore;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    protected $readFile = __DIR__ . "/mock/read.json";
    protected $writeFile = __DIR__ . "/mock/write.json";
    protected $setFile = __DIR__ . "/mock/meow.json";

    protected function createContainer()
    {
        $factory = new ContainerFactory;
        $factory->add(new CrapProvider(__DIR__));

        return $factory->createContainer();
    }

    protected function createFileStore($path)
    {
        $flags = JsonFileStore::NO_SERIALIZE_STRINGS | JsonFileStore::PRETTY_PRINT | JsonFileStore::NO_ESCAPE_SLASH;
        return new JsonFileStore($path, $flags);
    }
}
