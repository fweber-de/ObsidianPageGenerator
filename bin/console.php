<?php

require __DIR__.'/../vendor/autoload.php';

use ObsPager\Command\GeneratePagesCommand;
use ObsPager\GeneratorConfig;
use Symfony\Component\Console\Application;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

//config
$config = GeneratorConfig::fromFile(__DIR__.'/../config/generator.yml');

//twig
$loader = new FilesystemLoader(__DIR__.'/../themes/'.$config->theme);
$twig = new Environment($loader, []);

$application = new Application();
$application->add(new GeneratePagesCommand($twig, $config));
$application->run();
