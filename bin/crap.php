<?php

use Geekish\Crap\Crap;
use Geekish\Crap\CrapProvider;
use mindplay\unbox\ContainerFactory;

$root = dirname(__DIR__);

$tryRequire = function ($file) use ($root) {
    $path = sprintf("%s/%s", $root, $file);
    return file_exists($path) ? require_once $path : false;
};

if (!$tryRequire("/vendor/autoload.php") && !$tryRequire("/../../autoload.php")) {
    echo "Could not find autoloader; try running `composer install`." . PHP_EOL;
    exit(1);
}

$home = sprintf("%s/.composer", getenv("HOME"));

if (!empty(getenv("COMPOSER_HOME"))) {
    $home = getenv("COMPOSER_HOME");
}

$factory = new ContainerFactory;
$factory->add(new CrapProvider($home));
$container = $factory->createContainer();

$crap = $container->get(Crap::class);
$result = $container->call([$crap, "run"]);

exit($result);
