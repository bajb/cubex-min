<?php
namespace CubexMin;

use Cubex\Cubex;
use Packaged\Context\Context;
use Packaged\Routing\Handler\Handler;
use Workerman\Connection\ConnectionInterface;
use Workerman\Protocols\Http\Request;
use Workerman\Worker;

class CubexWorkerman extends Worker
{
  public function __construct($socket_name = '', array $context_option = [])
  {
    parent::__construct($socket_name, $context_option);
    $this->onMessage = [$this, 'onMessage'];
  }

  protected $_loader;

  /**
   * @param mixed $loader
   */
  public function setComposerLoader($loader)
  {
    $this->_loader = $loader;
    return $this;
  }

  public function setHandler(callable $handleGenerator)
  {
    $this->_handler = $handleGenerator;
    return $this;
  }

  /**
   * @var callable
   */
  protected $_handler;

  protected function _makeHandler(): ?Handler
  {
    $gen = $this->_handler;
    return $gen();
  }

  public function start()
  {
    Worker::runAll();
  }

  public function onMessage(ConnectionInterface $connection, Request $request)
  {
    $cReq = \Packaged\Http\Request::create(
      $request->uri(),
      $request->method(),
      $request->method() === 'POST' ? $request->post() : $request->get(),
      (array)$request->cookie(),
      $request->file(),
      [],
      $request->rawBody()
    );
    $cubex = new Cubex(dirname(__DIR__), $this->_loader);
    $cubex->share(Context::class, $cubex->prepareContext(new Context($cReq)));
    $response = $cubex->handle($this->_makeHandler(), false);

    // Send data to client
    $connection->send($response->getContent());

    $cubex->shutdown();
  }

}
