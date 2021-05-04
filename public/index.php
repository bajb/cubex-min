<?php
define('PHP_START', microtime(true));

use Cubex\Cubex;
use CubexMin\Application;
use CubexMin\CubexWorkerman;

$loader = require_once(dirname(__DIR__) . '/vendor/autoload.php');
try
{
  $useWorker = true;
  if($useWorker)
  {
    $worker = new CubexWorkerman('http://0.0.0.0:3000');
    $worker->setComposerLoader($loader)
      ->setHandler(function () { return new Application(); })
      ->start();
  }
  else
  {
    $cubex = new Cubex(dirname(__DIR__), $loader);
    $cubex->handle(new Application());
  }
}
catch(Throwable $e)
{

}
finally
{
  if($cubex instanceof Cubex)
  {
    //Call the shutdown command
    $cubex->shutdown();
  }
}
