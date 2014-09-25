<?php
/*
 * User: TFleet
 * Date: 2013-05-11
 * Time: 9:23 PM
 */

namespace TFleet\AuthToken;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\UserProviderInterface;
use TFleet\AuthToken\Exceptions\NotAuthorizedException;

class AuthTokenDriver {
  /**
   * @var \TFleet\AuthToken\AuthTokenProviderInterface
   */
  protected $tokens;

  /**
   * @var \Illuminate\Auth\UserProviderInterface
   */
  protected $users;

  function __construct(AuthTokenProviderInterface $tokens, UserProviderInterface $users)
  {
    $this->tokens = $tokens;
    $this->users = $users;
  }

  /**
   * Returns the AuthTokenInterface provider.
   *
   * @return \TFleet\AuthToken\AuthTokenProviderInterface
   */
  public function getProvider()
  {
    return $this->tokens;
  }


  /**
   * Validates a public auth token. Returns User object on success, otherwise false.
   *
   * @param $authTokenPayload
   * @return bool|UserInterface
   */
  public function validate($authTokenPayload,$user_agent) {

    if($authTokenPayload == null) {
      return false;
    }

    $tokenResponse = $this->tokens->find($authTokenPayload, $user_agent);

    if($tokenResponse == null) {
      return false;
    }

    $user = $this->users->retrieveByID( $tokenResponse->getAuthIdentifier() );

    if($user == null) {
      return false;
    }

    return $user;
  }

  /**
   * Attempt to create an AuthToken from user credentials.
   *
   * @param array $credentials
   * @return bool|AuthToken
   */
  public function attempt(array $credentials,$userAgent) {
    $user = $this->users->retrieveByCredentials($credentials);

    if($user instanceof UserInterface && $this->users->validateCredentials($user, $credentials)) {
       return $this->create($user,$userAgent);
    }

    return false;
  }

  /**
   * Create auth token for user.
   *
   * @param UserInterface $user
   * @return bool|AuthToken
   */
  public function create(UserInterface $user,$userAgent) {
    $this->tokens->purge($user,$userAgent);
    return $this->tokens->create($user,$userAgent);
  }

  /**
   * Retrive user from auth token.
   *
   * @param AuthToken $token
   * @return UserInterface|null
   */
  public function user(AuthToken $token) {
    return $this->users->retrieveByID( $token->getAuthIdentifier() );
  }

  /**
   * Serialize token for public use.
   *
   * @param AuthToken $token
   * @return string
   */
  public function publicToken(AuthToken $token) {
    return $this->tokens->serializeToken($token);
  }
}