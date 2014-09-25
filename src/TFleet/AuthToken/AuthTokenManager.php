<?php
/*
 * User: TFleet
 * Date: 2013-05-11
 * Time: 9:14 PM
 */

namespace TFleet\AuthToken;


use Illuminate\Support\Manager;

class AuthTokenManager extends Manager {

  protected function createDatabaseDriver() {
    $provider = $this->createDatabaseProvider();
    $users = $this->app['auth']->driver()->getProvider();

    return new AuthTokenDriver($provider, $users);
  }

  protected function createDatabaseProvider() {
    $connection = $this->app['db']->connection();
    $encrypter = $this->app['encrypter'];
    $hasher = new HashProvider($this->app['config']['app.key']);

    return new DatabaseAuthTokenProvider($connection, 'tokens', $encrypter, $hasher);
  }

  public function getDefaultDriver() {
    return 'database';
  }
}
