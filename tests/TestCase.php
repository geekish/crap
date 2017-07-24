<?php

namespace Geekish\Crap;

use PHPUnit\Framework\TestCase as BaseTestCase;
use mindplay\unbox\ContainerFactory;
use Webmozart\KeyValueStore\JsonFileStore;

/**
 * Class TestCase
 * @package Geekish\Crap
 */
abstract class TestCase extends BaseTestCase
{
    protected $readFile = __DIR__ . '/mock/read.json';
    protected $writeFile = __DIR__ . '/mock/write.json';
    protected $setFile = __DIR__ . '/mock/meow.json';

    /**
     * Factory method for creating Container for testing
     *
     * @return \mindplay\unbox\Container
     */
    protected function createContainer()
    {
        $factory = new ContainerFactory;
        $factory->add(new CrapProvider(__DIR__));

        return $factory->createContainer();
    }

    /**
     * Factory method for creating a JsonFileStore for testing
     *
     * @param string $path
     * @return JsonFileStore
     */
    protected function createFileStore($path)
    {
        $flags = JsonFileStore::NO_SERIALIZE_STRINGS | JsonFileStore::PRETTY_PRINT | JsonFileStore::NO_ESCAPE_SLASH;
        return new JsonFileStore($path, $flags);
    }
}
