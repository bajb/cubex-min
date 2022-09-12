<?php
define('PHP_START', microtime(true));

use Cubex\Cubex;
use Cubex\Workerman\CubexWorker;
use CubexMin\Application;

$loader = require_once(dirname(__DIR__) . '/vendor/autoload.php');
try
{
  $useWorker = true;
  if($useWorker)
  {
    $worker = CubexWorker::create(
      dirname(__DIR__),
      $loader,
      function () { return new Application(); },
      'http://0.0.0.0:3000'
    )->setCount(4);
    $worker->start();
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
