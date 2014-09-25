<?php
/**
 * User: TFleet
 * To change this template use File | Settings | File Templates.
 */

namespace TFleet\AuthToken;


use Illuminate\Auth\UserInterface;

/**
 * Class AuthTokenProviderInterface
 * @package TFleet\AuthToken
 */
interface AuthTokenProviderInterface {


  /**
   * Creates an auth token for user.
   *
   * @param \Illuminate\Auth\UserInterface $user
   * @param user agent
   * @return \TFleet\AuthToken\AuthToken|false
   */
  public function create(UserInterface $user,$userAgent);


  /**
   * Find user id from auth token.
   *
   * @param $serializedAuthToken string
   * @param $user_agent string
   * @return \TFleet\AuthToken\AuthToken|null
   */
  public function find($serializedAuthToken,$userAgent);

  /**
   * Returns serialized token.
   *
   * @param AuthToken $token
   * @return string
   */
  public function serializeToken(AuthToken $token);

  /**
   * Deserializes token.
   *
   * @param string $payload
   * @return AuthToken
   */
  public function deserializeToken($payload);

  /**
   * @param mixed|\Illuminate\Auth\UserInterface $identifier
   * @param user agent
   * @return bool
   */
  public function purge($identifier,$userAgent);
}