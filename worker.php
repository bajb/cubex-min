<?php

use Cubex\Context\Context;
use Cubex\Cubex;
use CubexMin\Application;
use Workerman\Connection\ConnectionInterface;
use Workerman\Protocols\Http\Request;
use Workerman\Worker;

$loader = require_once(__DIR__ . '/vendor/autoload.php');
// #### http worker ####
$http_worker = new Worker('http://0.0.0.0:2345');

// 4 processes
$http_worker->count = 4;

//Startup Cubex
// Emitted when data received
$http_worker->onMessage = function (ConnectionInterface $connection, Request $request) use ($loader) {
  $cReq = \Packaged\Http\Request::create(
    $request->uri(),
    $request->method(),
    $request->method() === 'POST' ? $request->post() : $request->get(),
    (array)$request->cookie(),
    $request->file(),
    [],
    $request->rawBody()
  );
  $cubex = new Cubex(dirname(__DIR__), $loader);
  $cubex->share(\Packaged\Context\Context::class, $cubex->prepareContext(new Context($cReq)));
  $response = $cubex->handle(new Application(), false);

  // Send data to client
  $connection->send($response->getContent());

  $cubex->shutdown();
};

// Run all workers
Worker::runAll();
