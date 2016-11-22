<?php

namespace Geekish\Crap;

use mindplay\unbox\ContainerFactory;
use Webmozart\KeyValueStore\JsonFileStore;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
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
