<?php

namespace Geekish\Crap;

use Composer\Package\Version\VersionParser;
use Exception;
use Webmozart\KeyValueStore\JsonFileStore;

/**
 * Class CrapHelper
 * @package Geekish\Crap
 */
class CrapHelper
{
    /** @var JsonFileStore */
    private $file;

    /** @var VersionParser */
    private $parser;

    /**
     * Construct CrapHelper
     *
     * @param JsonFileStore $file
     * @param VersionParser $parser
     */
    public function __construct(JsonFileStore $file, VersionParser $parser)
    {
        $this->file = $file;
        $this->parser = $parser;
    }

    /**
     * Get JsonFileStore
     *
     * @return JsonFileStore
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set JsonFileStore
     *
     * @param JsonFileStore $file
     */
    public function setFile(JsonFileStore $file)
    {
        $this->file = $file;
    }

    /**
     * Get VersionParser
     *
     * @return VersionParser
     */
    public function getVersionParser()
    {
        return $this->parser;
    }

    /**
     * Get aliases from FileStore
     *
     * @return array
     */
    public function getAliases()
    {
        return $this->file->keys();
    }

    /**
     * Get alias from FileStore
     *
     * @param string $alias
     * @throws Exception If alias is not found in JSON file.
     * @return string
     */
    public function getAlias($alias)
    {
        return $this->file->get($alias);
    }

    /**
     * Set alias in FileStore
     *
     * @param string $alias
     * @param $package
     * @return void
     */
    public function setAlias($alias, $package)
    {
        $this->file->set($alias, $package);
        $this->file->sort();
    }

    /**
     * Remove alias from FileStore
     *
     * @param string $alias
     * @return bool
     */
    public function unsetAlias($alias)
    {
        return $this->file->remove($alias);
    }

    /**
     * Check alias exists in FileStore
     *
     * @param string $alias
     * @return bool
     */
    public function hasAlias($alias)
    {
        return $this->file->exists($alias);
    }

    /**
     * Check that alias is valid
     *
     * @param string $alias
     * @return bool
     */
    public function validateAlias($alias)
    {
        return (bool) preg_match('{^[a-z0-9_.-]+$}', $alias);
    }

    /**
     * Make sure provided package string is valid
     *
     * @param string $input
     * @return boolean
     */
    public function validatePackage($input)
    {
        if (empty($input)) {
            return false;
        }

        list($package, $version) = $this->parsePackageToArray($input);

        if (!preg_match('{^[a-z0-9_.-]+/[a-z0-9_.-]+$}', $package)) {
            return false;
        }

        if ($version !== null) {
            try {
                $this->parser->parseConstraints($version);
            } catch (Exception $e) {
                return false;
            }
        }

        return true;
    }

    /**
     * Parse package argument array to string
     * Version is null if no constraint is provided
     *
     * @param array $arguments
     * @param bool $excludeVersions
     * @return array
     *
     * @throws CrapException If the alias is not set with crap
     */
    public function parseArguments(array $arguments, $excludeVersions = false)
    {
        return array_map(function ($arg) use ($excludeVersions) {
            if ($this->validatePackage($arg)) {
                return $arg;
            }

            list($alias, $argVersion) = $this->parsePackageToArray($arg);

            if (!$this->hasAlias($alias)) {
                throw CrapException::create('No record found for alias `%s`.', $alias);
            }

            list($package, $packageVersion) = $this->parsePackageToArray($this->getAlias($alias));

            $version = null;

            if (!$excludeVersions) {
                $version = $argVersion ?: $packageVersion;
            }

            return (is_null($version)) ? $package : sprintf('%s:%s', $package, $version);
        }, $arguments);
    }

    /**
     * Parse package argument string to array[package, version]
     * Version is null if no constraint is provided
     *
     * @param string $input
     * @return array
     */
    protected function parsePackageToArray($input)
    {
        $result = $this->parser->parseNameVersionPairs([$input])[0];

        if (!isset($result['version'])) {
            return [$result['name'], null];
        }

        return [$result['name'], $result['version']];
    }
}
