<?php
/*
 * User: tfleet
 */

namespace Tfleet\Support\Facades;

use Illuminate\Support\Facades\Facade;

class AuthToken extends Facade {

  protected static function getFacadeAccessor() { return 'tfleet.auth.token'; }
}