<?php
namespace CubexMin;

use Cubex\Controller\Controller;

class Application extends Controller
{
  public static $launched = 0;

  public function __construct()
  {
    static::$launched++;
  }

  protected function _generateRoutes()
  {
    yield self::_route('/user/{id}', 'user');
    yield self::_route('/user', 'user');
    yield self::_route('/', 'home');
  }

  public function postUser()
  {
    return "OK";
  }

  public function getUser()
  {
    return $this->routeData()->get('id');
  }

  public function getHome()
  {
    return "OK" . static::$launched;
  }
}
