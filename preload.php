<?php

use Cubex\Context\Context;
use Cubex\Cubex;
use CubexMin\Application;
use Packaged\Http\Request;

$loader = include('vendor/autoload.php');

//Load the basics
$cubex = new Cubex(__DIR__, $loader);
$context = new Context(new Request());
$cubex->handle(new Application());
