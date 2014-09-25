<?php
/*
 * User: tappleby
 * Date: 2013-05-11
 * Time: 4:07 PM
 */

namespace TFleet\AuthToken;

use \Illuminate\Auth\UserInterface;
use \Illuminate\Database\Connection;
use Illuminate\Encryption\Encrypter;

class DatabaseAuthTokenProvider extends AbstractAuthTokenProvider {

  /**
   * @var \Illuminate\Database\Connection
   */
  protected $conn;

  protected $table;

  /**
   * @param Connection $conn
   * @param string $table
   * @param \Illuminate\Encryption\Encrypter $encrypter
   * @param \TFleet\AuthToken\HashProvider $hasher
   */
  function __construct(Connection $conn, $table, Encrypter $encrypter, HashProvider $hasher)
  {
    parent::__construct($encrypter, $hasher);
    $this->table = $table;
    $this->conn = $conn;
  }

  /**
   * @return \Illuminate\Database\Connection
   */
  public function getConnection()
  {
    return $this->conn;
  }

  /**
   * @return \Illuminate\Database\Query\Builder
   */
  protected function db() {
    return $this->conn->table($this->table);
  }

  /**
   * Creates an auth token for user.
   *
   * @param \Illuminate\Auth\UserInterface $user
   * @param user agent
   * @return \TFleet\AuthToken\AuthToken|false
   */
  public function create(UserInterface $user,$userAgent)
  {
    if($user == null || $user->getAuthIdentifier() == null) {
      return false;
    }

    $token = $this->generateAuthToken(null,$userAgent);
    $token->setAuthIdentifier( $user->getAuthIdentifier() );
    $token->setUserAgent($userAgent);

    $t = new \DateTime;
    $insertData = array_merge($token->toArray(), array(
       'created_at' => $t, 'updated_at' => $t
    ));

    $this->db()->insert($insertData);

    return $token;
  }

  /**
   * Find user id from auth token.
   *
   * @param $serializedAuthToken string
   * @return \TFleet\AuthToken\AuthToken|null
   */
  public function find($serializedAuthToken,$user_agent)
  {
    $authToken = $this->deserializeToken($serializedAuthToken);

    if($authToken == null) {
      return null;
    }

    if(!$this->verifyAuthToken($authToken)) {
      return null;
    }

     $res = $this->db()
                ->where('auth_identifier', $authToken->getAuthIdentifier())
                ->where('public_key', $authToken->getPublicKey())
                ->where('private_key', $authToken->getPrivateKey())
                ->where('user_agent', $user_agent)
                ->first();

    if($res == null) {
      return null;
    }

    $now = time();
    $tokenTimeStamp = strtotime($res->created_at,null);

    // Todo: try to get it from the module config file...
    $tokenValidity = \Config::get('app.token_validity');

    if (!$tokenValidity) {
        // default is one day
        $tokenValidity = 86400 ;
    }

    // check if the token is expired.
    if ( ($now - $tokenTimeStamp) > $tokenValidity  ) {
        return null;
    }

    return $authToken;
  }

  /**
   * @param mixed|\Illuminate\Auth\UserInterface $identifier
   * @return bool
   */
  public function purge($identifier,$userAgent)
  {
    if($identifier instanceof UserInterface) {
      $identifier = $identifier->getAuthIdentifier();
    }

    $res = $this->db()->where('auth_identifier', $identifier)
                      ->where('user_agent',$userAgent)
                      ->delete();

    return $res > 0;
  }
}